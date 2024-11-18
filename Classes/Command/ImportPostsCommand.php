<?php

declare(strict_types=1);

/*
 * This file is part of the Extension "instagram" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace SvenPetersen\Instagram\Command;

use SvenPetersen\Instagram\Domain\Model\Feed;
use SvenPetersen\Instagram\Domain\Repository\FeedRepository;
use SvenPetersen\Instagram\Factory\ApiClientFactoryInterface;
use SvenPetersen\Instagram\Service\PostUpserter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;

class ImportPostsCommand extends Command
{
    public function __construct(
        private readonly FeedRepository $feedRepository,
        private readonly ApiClientFactoryInterface $apiClientFactory,
        private readonly PostUpserter $postUpserter,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp('Imports posts for a given instagram user access token')
            ->addArgument('username', InputArgument::REQUIRED, 'Instagram Username to import images for')
            ->addArgument('storagePid', InputArgument::REQUIRED, 'The PID where to save the image records')
            ->addArgument('limit', InputArgument::OPTIONAL, 'The maximum number of posts to upsert', 25)
            ->addOption('since', 's', InputOption::VALUE_OPTIONAL, 'Value that points to the beginning of a range of time-based data. (Format: "mm/dd/yyyy H:i:s")')
            ->addOption('until', 'u', InputOption::VALUE_OPTIONAL, 'Value that points to the end of a range of time-based data. (Format: "mm/dd/yyyy H:i:s")')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $username = $input->getArgument('username');
        /** @var int<0,max> $storagePid */
        $storagePid = $input->getArgument('storagePid');
        $limit = $input->getArgument('limit');

        $since = $input->getOption('since');
        $since = $since ? strtotime($since) : null;

        $until = $input->getOption('until');
        $until = $until ? strtotime($until) : null;

        if ($since === false || $until === false) {
            $output->writeln(sprintf('<fg=red>The option "since" and/or "until" is not valid strtotime format.</>'));

            return Command::FAILURE;
        }

        if (is_numeric($limit) === false) {
            $output->writeln(sprintf('<fg=red>The limit argument must be an Integer. "%s" given.</>', $limit));

            return Command::FAILURE;
        }

        /** @var Typo3QuerySettings $querySettings */
        $querySettings = GeneralUtility::makeInstance(Typo3QuerySettings::class);
        $querySettings->setRespectStoragePage(false);
        $this->feedRepository->setDefaultQuerySettings($querySettings);

        /** @var Feed|null $feed */
        $feed = $this->feedRepository->findOneBy(['username' => $username]);

        if ($feed === null) {
            $output->writeln(sprintf('<fg=red>No feed entity found for given username "%s".</>', $username));

            return Command::FAILURE;
        }

        $apiClient = $this->apiClientFactory->create($feed);

        $posts = $apiClient->getPosts((int)$limit, $since, $until);

        foreach ($posts as $postDTO) {
            $this->postUpserter->upsertPost($postDTO, (int)$storagePid, $apiClient);
        }

        $output->writeln(sprintf('<fg=green>The posts have been successfully imported/updated</>'));

        return Command::SUCCESS;
    }
}
