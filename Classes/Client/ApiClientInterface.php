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

interface ApiClientInterface
{
    /**
     * @param ?int $since UNIX Timestamp to return posts since
     * @param ?int $until UNIX Timestamp to return posts until
     *
     * @return PostDTO[]
     */
    public function getPosts(int $limit = 25, int $since = null, int $until = null): array;

    public function getFeedData(): FeedDTO;

    public function getFeed(): Feed;
}
