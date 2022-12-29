<?php

declare(strict_types=1);

namespace SvenPetersen\Instagram\Client;

use Psr\Http\Message\RequestFactoryInterface;
use SvenPetersen\Instagram\Domain\Model\FeedInterface;
use SvenPetersen\Instagram\Factory\PostDTOFactory;

class ApiClient implements ApiClientInterface
{
    private FeedInterface $feed;

    private RequestFactoryInterface $requestFactory;

    private string $apiBaseUrl;

    public function __construct(
        FeedInterface $feed,
        RequestFactoryInterface $requestFactory,
        string $apiBaseUrl
    ) {
        $this->feed = $feed;
        $this->requestFactory = $requestFactory;
        $this->apiBaseUrl = $apiBaseUrl;
    }

    /**
     * @return string[]
     */
    public function getDefaultMediaFields(): array
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
        ];
    }

    public function getPosts(int $limit = 25): array
    {
        $return = [];

        $endpoint = sprintf(
            '%s/%s/media/?access_token=%s&fields=%s',
            $this->apiBaseUrl,
            $this->feed->getUserId(),
            $this->feed->getToken(),
            implode(',', $this->getDefaultMediaFields())
        );

        $response = $this->request($endpoint);
        $posts = $response['data'];

        foreach ($posts as $postData) {
            $return[] = PostDTOFactory::create($postData);

            if (count($return) >= $limit) {
                break;
            }
        }

        // Get paginated posts if nessesary
        $nextPageUrl = $response['paging']['next'] ?? null;

        if (count($return) < $limit && $nextPageUrl) {
            $return = $this->getPaginatedPosts($return, $limit, $nextPageUrl);
        }

        return $return;
    }

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

    public function getMedia(string $mediaId, array $fields = null)
    {
        $fields = $fields ?? $this->getDefaultMediaFields();
        $fieldsString = strtolower(implode(',', $fields));

        $endpoint = sprintf(
            '%s/%s?fields=%s&access_token=%s',
            $this->apiBaseUrl,
            $mediaId,
            $fieldsString,
            $this->feed->getToken()
        );

        return $this->request($endpoint);
    }

    /**
     * @return int[]
     *
     * @throws \Exception
     */
    public function getChildrenMediaIds(string $mediaId): array
    {
        $endpoint = sprintf('%s/%s/children?access_token=%s', $this->apiBaseUrl, $mediaId, $this->feed->getToken());
        $response = $this->request($endpoint);

        $return = [];

        foreach ($response['data'] as $imageData) {
            $return[] = (int)$imageData['id'];
        }

        return $return;
    }

    /**
     * @param string[] $mediaIds
     *
     * @return array
     */
    public function getCarouselMedia(array $mediaIds): array
    {
        $return = [];

        /** @var string $mediaId */
        foreach ($mediaIds as $mediaId) {
            $return[] = $this->getMedia($mediaId, ['media_url', 'media_type']);
        }

        return $return;
    }

    public function getUserdata(): array
    {
        $endpoint = sprintf(
            '%s/%s/?access_token=%s&fields=id,username,account_type,media_count',
            $this->apiBaseUrl,
            $this->feed->getUserId(),
            $this->feed->getToken()
        );

        return $this->request($endpoint);
    }

    /**
     * @return mixed[]
     *
     * @throws \Exception
     */
    private function request(string $url, string $method = 'GET', array $additionalOptions = []): array
    {
        $response = $this->requestFactory->request($url, $method, $additionalOptions);

        if ($response->getStatusCode() !== 200) {
            throw new \Exception($response);
        }

        $contents = $response->getBody()->getContents();

        return json_decode($contents, true);
    }

    public function getFeed(): FeedInterface
    {
        return $this->feed;
    }
}
