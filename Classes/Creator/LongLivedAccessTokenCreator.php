<?php

declare(strict_types=1);

namespace SvenPetersen\Instagram\Creator;

use SvenPetersen\Instagram\Domain\Model\Longlivedaccesstoken;
use SvenPetersen\Instagram\Domain\Repository\LonglivedaccesstokenRepository;
use SvenPetersen\Instagram\Service\InstagramService;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;

final class LongLivedAccessTokenCreator
{
    private InstagramService $instagramService;

    private LonglivedaccesstokenRepository $tokenRepository;

    private PersistenceManagerInterface $persistenceManager;

    public function __construct(
        InstagramService $instagramService,
        LonglivedaccesstokenRepository $tokenRepository,
        PersistenceManagerInterface $persistenceManager
    ) {
        $this->instagramService = $instagramService;
        $this->tokenRepository = $tokenRepository;
        $this->persistenceManager = $persistenceManager;
    }

    public function create(string $clientSecret, string $accessToken, int $instagramUserId): Longlivedaccesstoken
    {
        $longLivedAccessToken = $this->instagramService->getLongLivedAccessToken(
            $clientSecret,
            $accessToken,
            $instagramUserId
        );

        $longLivedAccessToken->setPid(0);
        $this->tokenRepository->removeAll();
        $this->tokenRepository->add($longLivedAccessToken);

        $this->persistenceManager->persistAll();

        return $longLivedAccessToken;
    }
}
