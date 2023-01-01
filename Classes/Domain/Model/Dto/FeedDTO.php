<?php

declare(strict_types=1);

/*
 * This file is part of the Extension "instagram" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace SvenPetersen\Instagram\Domain\Model\Dto;

class FeedDTO
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
