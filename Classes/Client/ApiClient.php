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
    public function __construct(
        private readonly Feed $feed,
        private readonly RequestFactory $requestFactory,
        private readonly string $apiBaseUrl,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getPosts(int $limit = 25, int $since = null, int $until = null): array
    {
        $return = [];

        $endpoint = sprintf(
            '%s/%s/media/?access_token=%s&fields=%s',
            $this->apiBaseUrl,
            $this->feed->getUserId(),
            $this->feed->getToken(),
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
        $endpoint = sprintf(
            '%s/%s/?access_token=%s&fields=id,username,account_type,media_count',
            $this->apiBaseUrl,
            $this->feed->getUserId(),
            $this->feed->getToken()
        );

        $response = $this->request($endpoint);

        return FeedDTOFactory::createFromApiResponse($response);
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

    public function getFeed(): Feed
    {
        return $this->feed;
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
}
