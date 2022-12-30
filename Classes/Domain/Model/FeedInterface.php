<?php

declare(strict_types=1);

namespace SvenPetersen\Instagram\Domain\Model;

interface FeedInterface
{
    public function getUserId(): string;

    public function setUserId(string $userId): self;

    public function getToken(): string;

    public function setToken(string $token): self;

    public function getType(): string;

    public function setType(string $type): self;

    public function getExpiresAt(): ?\DateTimeImmutable;

    public function setExpiresAt(\DateTimeImmutable $expiresAt): self;

    public function getUsername(): string;

    public function setUsername(string $username): self;
}
