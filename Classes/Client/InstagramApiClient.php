<?php

declare(strict_types=1);

namespace SvenPetersen\Instagram\Client;

use Psr\Http\Message\RequestFactoryInterface;
use SvenPetersen\Instagram\Domain\Model\Longlivedaccesstoken;

final class InstagramApiClient
{
    private array $defaultMediaFields = [
        'id',
        'media_type',
        'thumbnail_url',
        'caption',
        'timestamp',
        'username',
        'media_url',
        'permalink',
    ];

    private RequestFactoryInterface $requestFactory;

    private string $accesstoken;

    private string $apiBaseUrl;

    public function __construct(RequestFactoryInterface $requestFactory, string $apiBaseUrl)
    {
        $this->requestFactory = $requestFactory;
        $this->apiBaseUrl = $apiBaseUrl;
    }

    public function getAuthorizationLink(string $instagramAppId, string $returnUri): string
    {
        return sprintf(
            'https://api.instagram.com/oauth/authorize/?client_id=%s&redirect_uri=%s&response_type=code&scope=user_profile,user_media',
            $instagramAppId,
            $returnUri
        );
    }

    public function getUserdata(string $userId): array
    {
        $endpoint = sprintf(
            '%s/%s/?access_token=%s&fields=id,username',
            $this->apiBaseUrl,
            $userId,
            $this->accesstoken
        );

        return $this->request($endpoint);
    }

    public function getLatestPosts(string $userId): array
    {
        $endpoint = sprintf('%s/%s/media/?access_token=%s', $this->apiBaseUrl, $userId, $this->accesstoken);

        return $this->request($endpoint);
    }

    public function getPostsRecursive(string $userId, string $endpoint = '', &$posts = []): array
    {
        if ('' === $endpoint) {
            // Initial request endpoint
            $endpoint = sprintf('%s/%s/media/?access_token=%s', $this->apiBaseUrl, $userId, $this->accesstoken);
        }

        $response = $this->request($endpoint);

        if (isset($response['paging']['next'])) {
            foreach ($response['data'] as $postData) {
                $posts[] = $postData;
            }

            $endpoint = $response['paging']['next'];

            $this->getPostsRecursive($userId, $endpoint, $posts);
        }

        return $posts;
    }

    public function getMedia(int $mediaId, array $fields = null)
    {
        $fields = $fields ?? $this->defaultMediaFields;
        $fieldsString = strtolower(implode(',', $fields));

        $endpoint = sprintf(
            '%s/%s?fields=%s&access_token=%s',
            $this->apiBaseUrl,
            $mediaId,
            $fieldsString,
            $this->accesstoken
        );

        return $this->request($endpoint);
    }

    /**
     * @return int[]
     */
    public function getChildrenMediaIds(string $mediaId): array
    {
        $endpoint = sprintf('%s/%s/children?access_token=%s', $this->apiBaseUrl, $mediaId, $this->accesstoken);
        $response = $this->request($endpoint);

        $return = [];

        foreach ($response['data'] as $postData) {
            $return[] = (int)$postData['id'];
        }

        return $return;
    }

    public function updateLongLivedAccessToken(Longlivedaccesstoken $token)
    {
        $endpoint = sprintf(
            '%s/refresh_access_token/?grant_type=ig_refresh_token&access_token=%s',
            $this->apiBaseUrl,
            $token->getToken()
        );

        return $this->request($endpoint);
    }

    public function setAccesstoken(string $accesstoken): self
    {
        $this->accesstoken = $accesstoken;

        return $this;
    }

    public function getAccessToken(string $clientId, string $clientSecret, string $redirectUri, string $code): array
    {
        $endpoint = 'https://api.instagram.com/oauth/access_token';

        $additionalOptions = [
            'form_params' => [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'code' => $code,
                'grant_type' => 'authorization_code',
                'redirect_uri' => $redirectUri,
            ],
        ];

        return $this->request($endpoint, 'POST', $additionalOptions);
    }

    public function requestLongLivedAccessToken(
        string $clientSecret,
        string $accessToken
    ): array {
        $endpoint = sprintf(
            'https://graph.instagram.com/access_token/?grant_type=ig_exchange_token&client_secret=%s&access_token=%s',
            $clientSecret,
            $accessToken
        );

        return $this->request($endpoint);
    }

    /**
     * @return mixed[]
     * @throws \Exception
     */
    private function request(string $url, string $method = 'GET', array $additionalOptions = []): array
    {
        $response = $this->requestFactory->request($url, $method, $additionalOptions);

        if (200 !== $response->getStatusCode()) {
            throw new \Exception($response);
        }

        $contents = $response->getBody()->getContents();

        return json_decode($contents, true);
    }
}
