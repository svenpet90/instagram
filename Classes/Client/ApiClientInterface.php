<?php

declare(strict_types=1);

namespace SvenPetersen\Instagram\Client;

use SvenPetersen\Instagram\Domain\Model\Dto\PostDTO;
use SvenPetersen\Instagram\Domain\Model\Dto\UserDTO;
use SvenPetersen\Instagram\Domain\Model\Feed;

interface ApiClientInterface
{
    /**
     * @return PostDTO[]
     */
    public function getPosts(int $limit = 25): array;

    public function getUserdata(): UserDTO;

    public function getFeed(): Feed;
}
