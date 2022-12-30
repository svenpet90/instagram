<?php

declare(strict_types=1);

namespace SvenPetersen\Instagram\Command;

use SvenPetersen\Instagram\Domain\Repository\FeedRepository;
use SvenPetersen\Instagram\Factory\ApiClientFactoryInterface;
use SvenPetersen\Instagram\Service\PostUpserter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportPostsCommand extends Command
{
    private FeedRepository $feedRepository;

    private ApiClientFactoryInterface $apiClientFactory;

    private PostUpserter $postUpserter;

    public function __construct(
        FeedRepository $feedRepository,
        ApiClientFactoryInterface $apiClientFactory,
        PostUpserter $postUpserter
    ) {
        $this->feedRepository = $feedRepository;
        $this->apiClientFactory = $apiClientFactory;
        $this->postUpserter = $postUpserter;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp('Imports posts for a given instagram user access token')
            ->addArgument('username', InputArgument::REQUIRED, 'Instagram Username to import images for')
            ->addArgument('storagePid', InputArgument::REQUIRED, 'The PID where to save the image records')
            ->addArgument('limit', InputArgument::OPTIONAL, 'The maximum number of posts to upsert', 25);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $username = $input->getArgument('username');
        $storagePid = $input->getArgument('storagePid');
        $limit = (int)$input->getArgument('limit');

        if (is_numeric($storagePid) === false) {
            throw new \InvalidArgumentException(sprintf('The StoragePid argument must be numeric. "%s" given.', $storagePid));
        }

        $feed = $this->feedRepository->findOneByUsername($username);

        if ($feed === null) {
            throw new \InvalidArgumentException(sprintf('No feed entity found for given username "%s".', $username));
        }

        $apiClient = $this->apiClientFactory->create($feed);

        $posts = $apiClient->getPosts($limit);

        foreach ($posts as $postDTO) {
            $this->postUpserter->upsertPost($postDTO, (int)$storagePid, $apiClient);
        }

        return Command::SUCCESS;
    }
}
