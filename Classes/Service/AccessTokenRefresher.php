<?php

declare(strict_types=1);

namespace SvenPetersen\Instagram\Service;

use DateTime;
use SvenPetersen\Instagram\Domain\Model\Feed;
use SvenPetersen\Instagram\Domain\Repository\FeedRepository;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;

/** @internal */
class AccessTokenRefresher
{
    private FeedRepository $feedRepository;

    private PersistenceManagerInterface $persistenceManager;

    private AccessTokenService $accessTokenService;

    public function __construct(
        FeedRepository $feedRepository,
        PersistenceManagerInterface $persistenceManager,
        AccessTokenService $accessTokenService
    ) {
        $this->feedRepository = $feedRepository;
        $this->persistenceManager = $persistenceManager;
        $this->accessTokenService = $accessTokenService;
    }

    /**
     * @return Feed[]
     */
    public function refreshAll(): array
    {
        $return = [];
        $feeds = $this->feedRepository->findAll();

        /** @var Feed $feed */
        foreach ($feeds as $feed) {
            $return[] = $this->refreshAccessToken($feed);
        }

        return $return;
    }

    public function refreshAccessToken(Feed $feed): Feed
    {
        $expiresAt = $feed->getExpiresat();
        $now = new DateTime();
        $diffInDays = $expiresAt->diff($now)->days;

        if ($diffInDays > 10) {
            // only update token if it's valid for less than 10 days
            return $feed;
        }

        $updatedTokenData = $this->accessTokenService->refreshAccessToken($feed);

        $expiresAt = (new DateTime())->modify(sprintf('+ %s seconds', $updatedTokenData['expires_in']));

        $feed
            ->setToken($updatedTokenData['access_token'])
            ->setType($updatedTokenData['token_type'])
            ->setExpiresat($expiresAt);

        $this->feedRepository->update($feed);
        $this->persistenceManager->persistAll();

        return $token;
    }
}
