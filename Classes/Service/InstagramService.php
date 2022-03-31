<?php

declare(strict_types=1);

namespace SvenPetersen\Instagram\Service;

use DateTime;
use SvenPetersen\Instagram\Client\InstagramApiClient;
use SvenPetersen\Instagram\Domain\Model\Longlivedaccesstoken;
use SvenPetersen\Instagram\Domain\Repository\LonglivedaccesstokenRepository;
use SvenPetersen\Instagram\Factory\LonglivedaccesstokenFactory;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;

class InstagramService
{
    protected array $defaultMediaFields = [
        'id',
        'media_type',
        'thumbnail_url',
        'caption',
        'timestamp',
        'username',
        'media_url',
        'permalink',
    ];

    private LonglivedaccesstokenFactory $longlivedaccesstokenFactory;

    private InstagramApiClient $apiClient;

    private LonglivedaccesstokenRepository $longlivedaccesstokenRepository;

    private PersistenceManagerInterface $persistenceManager;

    public function __construct(
        LonglivedaccesstokenFactory $longlivedaccesstokenFactory,
        LonglivedaccesstokenRepository $longlivedaccesstokenRepository,
        PersistenceManagerInterface $persistenceManager,
        InstagramApiClient $apiClient
    ) {
        $this->longlivedaccesstokenFactory = $longlivedaccesstokenFactory;
        $this->apiClient = $apiClient;
        $this->longlivedaccesstokenRepository = $longlivedaccesstokenRepository;
        $this->persistenceManager = $persistenceManager;
    }

    public function getLongLivedAccessToken(
        string $clientSecret,
        string $shortLivedAccessToken,
        int $instagramUserId
    ): Longlivedaccesstoken {
        $responseArray = $this->apiClient->requestLongLivedAccessToken($clientSecret, $shortLivedAccessToken);

        if (! isset($responseArray['access_token'])) {
            throw new \Exception('Kein access_token Key in der Response!');
        }

        $token = $responseArray['access_token'];
        $type = $responseArray['token_type'];
        $expiresIn = $responseArray['expires_in'];
        $expiresAt = (new DateTime())->modify(sprintf('+ %s seconds', $expiresIn));

        return $this->longlivedaccesstokenFactory->create(
            $token,
            $type,
            $expiresAt,
            (string)$instagramUserId
        );
    }

    /**
     * @return Longlivedaccesstoken[]
     */
    public function refreshAllAccessTokens(): array
    {
        $return = [];
        $tokens = $this->longlivedaccesstokenRepository->findAll();

        /** @var Longlivedaccesstoken $token */
        foreach ($tokens as $token) {
            $return[] = $this->refreshLongLivedAccessToken($token);
        }

        return $return;
    }

    public function refreshLongLivedAccessToken(Longlivedaccesstoken $token): Longlivedaccesstoken
    {
        $expiresAt = $token->getExpiresat();
        $now = new DateTime();
        $diffInDays = $expiresAt->diff($now)->days;

        if ($diffInDays > 10) {
            // only update token if it's valid for less than 10 days
            return $token;
        }

        $updatedTokenData = $this->apiClient->updateLongLivedAccessToken($token);

        $expiresAt = (new DateTime())->modify(sprintf('+ %s seconds', $updatedTokenData['expires_in']));

        $token
            ->setToken($updatedTokenData['access_token'])
            ->setType($updatedTokenData['token_type'])
            ->setExpiresat($expiresAt)
        ;

        $this->longlivedaccesstokenRepository->update($token);
        $this->persistenceManager->persistAll();

        return $token;
    }

    public function getCarouselMedia(array $mediaIds): array
    {
        $return = [];

        /** @var int $mediaId */
        foreach ($mediaIds as $mediaId) {
            $return[] = $this->apiClient->getMedia($mediaId, ['media_url', 'media_type']);
        }

        return $return;
    }
}
