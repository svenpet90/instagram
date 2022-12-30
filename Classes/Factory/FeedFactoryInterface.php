<?php

declare(strict_types=1);

namespace SvenPetersen\Instagram\Factory;

use SvenPetersen\Instagram\Domain\Model\Feed;

interface FeedFactoryInterface
{
    public function upsert(string $token, string $type, string $userId, \DateTimeImmutable $expiresAt, string $username = ''): Feed;
}
