<?php

declare(strict_types=1);

namespace SvenPetersen\Instagram\Factory;

use Psr\Http\Message\RequestFactoryInterface;
use SvenPetersen\Instagram\Client\ApiClient;
use SvenPetersen\Instagram\Client\ApiClientInterface;
use SvenPetersen\Instagram\Domain\Model\FeedInterface;

/**
 * @internal
 */
class ApiClientFactory implements ApiClientFactoryInterface
{
    private RequestFactoryInterface $requestFactory;

    private string $apiBaseUrl;

    public function __construct(
        RequestFactoryInterface $requestFactory,
        string $apiBaseUrl
    ) {
        $this->requestFactory = $requestFactory;
        $this->apiBaseUrl = $apiBaseUrl;
    }

    public function create(FeedInterface $feed): ApiClientInterface
    {
        return new ApiClient($feed, $this->requestFactory, $this->apiBaseUrl);
    }
}
