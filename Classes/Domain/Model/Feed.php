<?php

declare(strict_types=1);

namespace SvenPetersen\Instagram\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class Feed extends AbstractEntity
{
    protected string $userId = '';

    protected string $token = '';

    protected string $tokenType = 'bearer';

    protected string $username = '';

    /**
     * @var ObjectStorage<\SvenPetersen\Instagram\Domain\Model\Post>
     */
    protected ?ObjectStorage $posts = null;

    protected ?\DateTimeImmutable $expiresAt = null;

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function setUserId(string $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getTokenType(): string
    {
        return $this->tokenType;
    }

    public function setTokenType(string $tokenType): self
    {
        $this->tokenType = $tokenType;

        return $this;
    }

    public function getExpiresAt(): ?\DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(\DateTimeImmutable $expiresAt): self
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return ObjectStorage<\SvenPetersen\Instagram\Domain\Model\Post>
     */
    public function getPosts(): ?ObjectStorage
    {
        return $this->posts;
    }

    /**
     * @param ObjectStorage<\SvenPetersen\Instagram\Domain\Model\Post> $posts
     */
    public function setPosts(ObjectStorage $posts): self
    {
        $this->posts = $posts;

        return $this;
    }
}
