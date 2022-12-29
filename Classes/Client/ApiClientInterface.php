<?php

declare(strict_types=1);

namespace SvenPetersen\Instagram\Client;

use SvenPetersen\Instagram\Domain\Model\FeedInterface;

interface ApiClientInterface
{
    public function getPosts(int $limit = 25): array;

    public function getUserdata(): array;

    public function getFeed(): FeedInterface;
}
