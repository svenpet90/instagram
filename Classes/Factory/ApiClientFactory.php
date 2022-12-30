<?php

declare(strict_types=1);

namespace SvenPetersen\Instagram\Factory;

use SvenPetersen\Instagram\Client\ApiClient;
use SvenPetersen\Instagram\Client\ApiClientInterface;
use SvenPetersen\Instagram\Domain\Model\Feed;
use TYPO3\CMS\Core\Http\RequestFactory;

/**
 * @internal
 */
class ApiClientFactory implements ApiClientFactoryInterface
{
    private RequestFactory $requestFactory;

    private string $apiBaseUrl;

    public function __construct(
        RequestFactory $requestFactory,
        string $apiBaseUrl
    ) {
        $this->requestFactory = $requestFactory;
        $this->apiBaseUrl = $apiBaseUrl;
    }

    public function create(Feed $feed): ApiClientInterface
    {
        return new ApiClient($feed, $this->requestFactory, $this->apiBaseUrl);
    }
}
