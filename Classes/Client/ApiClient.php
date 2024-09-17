<?php

declare(strict_types=1);

/*
 * This file is part of the Extension "instagram" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace SvenPetersen\Instagram\Client;

use SvenPetersen\Instagram\Domain\Model\Dto\FeedDTO;
use SvenPetersen\Instagram\Domain\Model\Dto\PostDTO;
use SvenPetersen\Instagram\Domain\Model\Feed;
use SvenPetersen\Instagram\Factory\Dto\FeedDTOFactory;
use SvenPetersen\Instagram\Factory\Dto\PostDTOFactory;
use TYPO3\CMS\Core\Http\RequestFactory;

class ApiClient implements ApiClientInterface
{
    private ?string $token;

    private ?string $userId;

    public function __construct(
        private readonly RequestFactory $requestFactory,
        private readonly string         $apiBaseUrl,
        private readonly string         $graphApiBaseUrl,
    ) {
    }

    public function setup(string $userId, string $token): self
    {
        $this->setToken($token);
        $this->setUserId($userId);

        return $this;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function setUserId(string $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPosts(int $limit = 25, int $since = null, int $until = null): array
    {
        $return = [];

        $endpoint = sprintf(
            '%s/%s/media/?access_token=%s&fields=%s',
            $this->graphApiBaseUrl,
            $this->userId,
            $this->token,
            implode(',', $this->getDefaultMediaFields())
        );

        if ($since) {
            $endpoint .= sprintf('&since=%d', $since);
        }

        if ($until) {
            $endpoint .= sprintf('&until=%d', $until);
        }

        $response = $this->request($endpoint);
        $posts = $response['data'];

        foreach ($posts as $postData) {
            $return[] = PostDTOFactory::create($postData);

            if (count($return) >= $limit) {
                break;
            }
        }

        // Get paginated posts if necessary
        $nextPageUrl = $response['paging']['next'] ?? null;

        if (count($return) < $limit && $nextPageUrl) {
            $return = $this->getPaginatedPosts($return, $limit, $nextPageUrl);
        }

        return $return;
    }

    /**
     * @param PostDTO[] $posts
     *
     * @return PostDTO[]
     *
     * @throws \Exception
     */
    private function getPaginatedPosts(array $posts, int $limit, string $nextPageUrl): array
    {
        while (count($posts) < $limit && $nextPageUrl) {
            $nextPageData = $this->request($nextPageUrl);
            $nextPageUrl = $nextPageData['paging']['next'] ?? null;

            foreach ($nextPageData['data'] as $postData) {
                $posts[] = PostDTOFactory::create($postData);

                if (count($posts) === $limit) {
                    break 2;
                }
            }
        }

        return $posts;
    }

    /**
     * @throws \Exception
     */
    public function getFeedData(): FeedDTO
    {
        $me = $this->me();

        return FeedDTOFactory::createFromApiResponse($me);
    }

    /**
     * @return mixed[]
     * @throws \Exception
     */
    public function me(?string $token = null): array
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
            '%s/me?access_token=%s&fields=%s',
            $this->graphApiBaseUrl,
            $token ?: $this->token,
            implode(',', $fields),
        );

        return $this->request($endpoint);
    }

    /**
     * @param array<string, mixed> $additionalOptions
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
     * @return string[]
     */
    private function getDefaultMediaFields(): array
    {
        return [
            'caption',
            'id',
            'is_shared_to_feed',
            'media_type',
            'media_url',
            'permalink',
            'thumbnail_url',
            'timestamp',
            'username',
            'children{id,media_type,media_url,thumbnail_url,timestamp,permalink,username}',
        ];
    }

    /**
     * @return mixed[]
     */
    public function refreshAccessToken(?string $token = null): array
    {
        $endpoint = sprintf(
            '%s/refresh_access_token/?grant_type=ig_refresh_token&access_token=%s',
            $this->graphApiBaseUrl,
            $token ?: $this->token
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
    ): array
    {
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
}
