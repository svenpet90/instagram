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
use SvenPetersen\Instagram\Factory\ApiClientFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;

final class UpdateFeedDataCommand extends Command
{
    public function __construct(
        private readonly FeedRepository $feedRepository,
        private readonly ApiClientFactory $apiClientFactory,
        private readonly PersistenceManager $persistenceManager,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp('Updates a Feeds meta info, like followers count, media count etc.')
            ->addArgument('username', InputArgument::REQUIRED, 'Instagram Username to import images for');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var Typo3QuerySettings $querySettings */
        $querySettings = GeneralUtility::makeInstance(Typo3QuerySettings::class);
        $querySettings->setRespectStoragePage(false);
        $this->feedRepository->setDefaultQuerySettings($querySettings);

        $username = $input->getArgument('username');

        $feed = $this->feedRepository->findOneBy(['username' => $username]);

        if (!$feed instanceof Feed) {
            return Command::FAILURE;
        }

        $apiClient = $this->apiClientFactory->create($feed);

        $me = $apiClient->me();
        $feed->updateFromArray($me);

        $this->feedRepository->update($feed);
        $this->persistenceManager->persistAll();

        return Command::SUCCESS;
    }
}
