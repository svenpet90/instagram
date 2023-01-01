<?php

declare(strict_types=1);

/*
 * This file is part of the Extension "instagram" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace SvenPetersen\Instagram\Factory;

use SvenPetersen\Instagram\Domain\Model\Feed;
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

    public function create(): Feed
    {
        return new Feed();
    }

    public function upsert(
        string $token,
        string $tokenType,
        string $userId,
        \DateTimeImmutable $expiresAt,
        string $username,
        int $storagePid
    ): Feed {
        $feed = $this->feedRepository->findOneByUsername($username);

        if ($feed === null) {
            $feed = $this->create();
        }

        $feed->setPid($storagePid);
        $feed
            ->setToken($token)
            ->setTokenType($tokenType)
            ->setExpiresAt($expiresAt)
            ->setUserId($userId)
            ->setUsername($username);

        $this->feedRepository->add($feed);
        $this->persistenceManager->persistAll();

        return $feed;
    }
}
