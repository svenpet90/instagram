<?php

declare(strict_types=1);

namespace SvenPetersen\Instagram\Service;

use SvenPetersen\Instagram\Domain\Model\Feed;
use SvenPetersen\Instagram\Factory\FeedFactoryInterface;
use TYPO3\CMS\Core\Http\RequestFactory;

/**
 * @internal
 */
class AccessTokenService
{
    private RequestFactory $requestFactory;

    private FeedFactoryInterface $feedFactory;

    private string $apiBaseUrl;

    private string $graphApiBaseUrl;

    public function __construct(
        RequestFactory $requestFactory,
        FeedFactoryInterface $feedFactory,
        string $apiBaseUrl,
        string $graphApiBaseUrl
    ) {
        $this->requestFactory = $requestFactory;
        $this->feedFactory = $feedFactory;
        $this->apiBaseUrl = $apiBaseUrl;
        $this->graphApiBaseUrl = $graphApiBaseUrl;
    }

    public function getLongLivedAccessToken(
        string $instagramAppId,
        string $clientSecret,
        string $redirect_uri,
        string $code
    ): Feed {
        $shortLivedAccessTokenData = $this->getAccessToken(
            $instagramAppId,
            $clientSecret,
            $redirect_uri,
            $code
        );

        $shortLivedAccessToken = $shortLivedAccessTokenData['access_token'];
        $userId = (string)$shortLivedAccessTokenData['user_id'];

        $responseArray = $this->requestLongLivedAccessToken($clientSecret, $shortLivedAccessToken);

        if (!isset($responseArray['access_token'])) {
            throw new \Exception('Kein access_token Key in der Response!');
        }

        $token = $responseArray['access_token'];
        $type = $responseArray['token_type'];
        $expiresIn = $responseArray['expires_in'];
        $expiresAt = (new \DateTimeImmutable())->modify(sprintf('+ %s seconds', $expiresIn));

        $userdata = $this->getUserdata($userId, $shortLivedAccessToken);
        $username = $userdata['username'];

        return $this->feedFactory->upsert(
            $token,
            $type,
            $userId,
            $expiresAt,
            $username
        );
    }

    /**
     * @return mixed[]
     */
    public function refreshAccessToken(Feed $feed): array
    {
        $endpoint = sprintf(
            '%s/refresh_access_token/?grant_type=ig_refresh_token&access_token=%s',
            $this->apiBaseUrl,
            $feed->getToken()
        );

        return $this->request($endpoint);
    }

    /**
     * @return mixed[] array{'access_token': string, 'user_id': int}
     *
     * @throws \Exception
     */
    public function getAccessToken(string $clientId, string $clientSecret, string $redirectUri, string $code): array
    {
        $endpoint = sprintf('%s/oauth/access_token', $this->apiBaseUrl);

        $additionalOptions = [
            'form_params' => [
                'grant_type' => 'authorization_code',
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'redirect_uri' => $redirectUri,
                'code' => rtrim($code, '#_'),
            ],
        ];

        return $this->request($endpoint, 'POST', $additionalOptions);
    }

    /**
     * @return mixed[]
     *
     * @throws \Exception
     */
    public function requestLongLivedAccessToken(
        string $clientSecret,
        string $accessToken
    ): array {
        $endpoint = sprintf(
            '%s/access_token/?grant_type=ig_exchange_token&client_secret=%s&access_token=%s',
            $this->graphApiBaseUrl,
            $clientSecret,
            $accessToken
        );

        return $this->request($endpoint);
    }

    public function getAuthorizationLink(string $instagramAppId, string $returnUri): string
    {
        return sprintf(
            '%s/oauth/authorize/?client_id=%s&redirect_uri=%s&response_type=code&scope=user_profile,user_media',
            $this->apiBaseUrl,
            $instagramAppId,
            $returnUri
        );
    }

    /**
     * @param array<string,mixed> $additionalOptions
     *
     * @return mixed[]
     *
     * @throws \Exception
     */
    private function request(string $url, string $method = 'GET', array $additionalOptions = []): array
    {
        $response = $this->requestFactory->request($url, $method, $additionalOptions);

        if ($response->getStatusCode() !== 200) {
            throw new \Exception($response->getReasonPhrase());
        }

        $contents = $response->getBody()->getContents();

        return json_decode($contents, true);
    }

    /**
     * @return mixed[] array{'id': string, 'username': string}
     *
     * @throws \Exception
     */
    public function getUserdata(string $userId, string $accessToken): array
    {
        $endpoint = sprintf(
            '%s/%s/?access_token=%s&fields=id,username',
            $this->graphApiBaseUrl,
            $userId,
            $accessToken
        );

        return $this->request($endpoint);
    }
}
