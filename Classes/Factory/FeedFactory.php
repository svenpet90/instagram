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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;

readonly class FeedFactory implements FeedFactoryInterface
{
    public function __construct(
        private FeedRepository $feedRepository,
        private PersistenceManagerInterface $persistenceManager
    ) {}

    public function create(): Feed
    {
        return new Feed();
    }

    /**
     * @param int<0, max> $storagePid
     */
    public function upsert(
        string $token,
        string $tokenType,
        string $userId,
        \DateTimeImmutable $expiresAt,
        string $username,
        int $storagePid
    ): Feed {
        /** @var Typo3QuerySettings $querySettings */
        $querySettings = GeneralUtility::makeInstance(Typo3QuerySettings::class);
        $querySettings->setRespectStoragePage(false);
        $this->feedRepository->setDefaultQuerySettings($querySettings);

        /** @var Feed|null $feed */
        $feed = $this->feedRepository->findOneBy(['username' => $username]);

        if ($feed === null) {
            $feed = $this->create();
        }

        $feed
            ->setToken($token)
            ->setTokenType($tokenType)
            ->setExpiresAt($expiresAt)
            ->setUserId($userId)
            ->setUsername($username)
            ->setPid($storagePid);

        $this->feedRepository->add($feed);
        $this->persistenceManager->persistAll();

        return $feed;
    }
}
