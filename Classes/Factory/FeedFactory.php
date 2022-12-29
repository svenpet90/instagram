<?php

declare(strict_types=1);

namespace SvenPetersen\Instagram\Factory;

use SvenPetersen\Instagram\Domain\Model\Feed;
use SvenPetersen\Instagram\Domain\Model\FeedInterface;
use SvenPetersen\Instagram\Domain\Repository\FeedRepository;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;

class FeedFactory implements FeedFactoryInterface
{
    private FeedRepository $feedRepository;

    private PersistenceManagerInterface $persistenceManager;

    public function __construct(
        FeedRepository $feedRepository,
        PersistenceManagerInterface $persistenceManager
    ) {
        $this->feedRepository = $feedRepository;
        $this->persistenceManager = $persistenceManager;
    }

    public function upsert(
        string $token,
        string $type,
        string $userId,
        \DateTimeImmutable $expiresAt,
        string $username = ''
    ): FeedInterface {
        $feed = $this->feedRepository->findOneByUsername($username);

        if ($feed === null) {
            $feed = new Feed();
        }

        $feed->setPid(0);
        $feed
            ->setToken($token)
            ->setType($type)
            ->setExpiresAt($expiresAt)
            ->setUserId($userId)
            ->setUsername($username);

        $this->feedRepository->add($feed);
        $this->persistenceManager->persistAll();

        return $feed;
    }
}
