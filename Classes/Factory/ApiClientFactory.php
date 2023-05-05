<?php

declare(strict_types=1);

/*
 * This file is part of the Extension "instagram" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

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
