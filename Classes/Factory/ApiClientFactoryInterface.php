<?php

declare(strict_types=1);

/*
 * This file is part of the Extension "instagram" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

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
