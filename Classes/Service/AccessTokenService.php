<?php

declare(strict_types=1);

/*
 * This file is part of the Extension "instagram" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace SvenPetersen\Instagram\Service;

use SvenPetersen\Instagram\Domain\Model\Feed;
use TYPO3\CMS\Core\Http\RequestFactory;

/**
 * @internal
 */
class AccessTokenService
{
    private RequestFactory $requestFactory;

    private string $apiBaseUrl;

    private string $graphApiBaseUrl;

    public function __construct(
        RequestFactory $requestFactory,
        string $apiBaseUrl,
        string $graphApiBaseUrl
    ) {
        $this->requestFactory = $requestFactory;
        $this->apiBaseUrl = $apiBaseUrl;
        $this->graphApiBaseUrl = $graphApiBaseUrl;
    }

    /**
     * @return mixed[]
     */
    public function refreshAccessToken(Feed $feed): array
    {
        $endpoint = sprintf(
            '%s/refresh_access_token/?grant_type=ig_refresh_token&access_token=%s',
            $this->graphApiBaseUrl,
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
    public function getLongLivedAccessToken(
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
            'https://www.instagram.com/oauth/authorize/?client_id=%s&redirect_uri=%s&response_type=code&scope=business_basic',
            $instagramAppId,
            $returnUri,
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
            throw new \HttpRequestException($response->getReasonPhrase());
        }

        $contents = $response->getBody()->getContents();

        return json_decode($contents, true);
    }

    /**
     * @return array<string, string>
     *
     * @throws \Exception
     */
    public function me(string $accessToken): array
    {
        $fields = [
            'id',
            'user_id',
            'username',
            'name',
            'account_type',
            'profile_picture_url',
            'followers_count',
            'follows_count',
            'media_count',
        ];

        $endpoint = sprintf(
            '%s/me/?access_token=%s&fields=%s',
            $this->graphApiBaseUrl,
            $accessToken,
            implode(',', $fields)
        );

        return $this->request($endpoint);
    }
}
