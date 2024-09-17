<?php

declare(strict_types=1);

/*
 * This file is part of the Extension "instagram" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace SvenPetersen\Instagram\Service;

use DateTime;
use SvenPetersen\Instagram\Client\ApiClient;
use SvenPetersen\Instagram\Domain\Model\Feed;
use SvenPetersen\Instagram\Domain\Repository\FeedRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;

/**
 * @internal
 */
class AccessTokenRefresher
{
    public function __construct(
        private readonly FeedRepository $feedRepository,
        private readonly PersistenceManagerInterface $persistenceManager,
        private readonly ApiClient $apiClient,
    ) {
    }

    /**
     * @return Feed[]
     */
    public function refreshAll(): array
    {
        $return = [];

        /** @var Typo3QuerySettings $querySettings */
        $querySettings = GeneralUtility::makeInstance(Typo3QuerySettings::class);
        $querySettings->setRespectStoragePage(false);

        $this->feedRepository->setDefaultQuerySettings($querySettings);
        $feeds = $this->feedRepository->findAll();

        /** @var Feed $feed */
        foreach ($feeds as $feed) {
            $return[] = $this->refreshAccessToken($feed);
        }

        return $return;
    }

    public function refreshAccessToken(Feed $feed): Feed
    {
        /** @var \DateTimeImmutable $expiresAt */
        $expiresAt = $feed->getExpiresat();
        $now = new DateTime();
        $diffInDays = $expiresAt->diff($now)->days;

        if ($diffInDays >= 10) {
            // only update token if it's valid for less than 10 days
            return $feed;
        }

        $updatedTokenData = $this->apiClient->refreshAccessToken($feed->getToken());

        $expiresAt = (new \DateTimeImmutable())->modify(sprintf('+ %s seconds', $updatedTokenData['expires_in']));

        $feed
            ->setToken($updatedTokenData['access_token'])
            ->setTokenType($updatedTokenData['token_type'])
            ->setExpiresat($expiresAt);

        $this->feedRepository->update($feed);
        $this->persistenceManager->persistAll();

        return $feed;
    }
}
