<?php

declare(strict_types=1);

namespace SvenPetersen\Instagram\Domain\DTO;

class UserDTO
{
    private string $id;

    private string $username;

    private string $accountType;

    private int $mediaCount;

    public function __construct(
        string $id,
        string $username,
        string $accountType,
        int $mediaCount
    ) {
        $this->id = $id;
        $this->username = $username;
        $this->accountType = $accountType;
        $this->mediaCount = $mediaCount;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getAccountType(): string
    {
        return $this->accountType;
    }

    public function getMediaCount(): int
    {
        return $this->mediaCount;
    }
}
