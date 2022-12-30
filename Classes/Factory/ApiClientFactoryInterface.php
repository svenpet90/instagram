<?php

declare(strict_types=1);

namespace SvenPetersen\Instagram\Factory;

use SvenPetersen\Instagram\Client\ApiClientInterface;
use SvenPetersen\Instagram\Domain\Model\Feed;

/**
 * @api
 */
interface ApiClientFactoryInterface
{
    public function create(Feed $feed): ApiClientInterface;
}
