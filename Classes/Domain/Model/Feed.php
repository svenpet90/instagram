<?php

declare(strict_types=1);

/*
 * This file is part of the Extension "instagram" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace SvenPetersen\Instagram\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class Feed extends AbstractEntity
{
    protected string $id = '';

    protected string $userId = '';

    protected string $username = '';

    protected string $name = '';

    protected string $accountType = '';

    protected string $profilePictureUrl = '';

    protected int $followersCount = 0;

    protected int $followsCount = 0;

    protected int $mediaCount = 0;

    protected string $token = '';

    protected string $tokenType = 'bearer';

    protected ?int $_languageUid = -1;

    /**
     * @var ObjectStorage<\SvenPetersen\Instagram\Domain\Model\Post>
     */
    protected ?ObjectStorage $posts = null;

    protected ?\DateTimeImmutable $expiresAt = null;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAccountType(): string
    {
        return $this->accountType;
    }

    public function setAccountType(string $accountType): self
    {
        $this->accountType = $accountType;

        return $this;
    }

    public function getProfilePictureUrl(): string
    {
        return $this->profilePictureUrl;
    }

    public function setProfilePictureUrl(string $profilePictureUrl): self
    {
        $this->profilePictureUrl = $profilePictureUrl;

        return $this;
    }

    public function getFollowersCount(): int
    {
        return $this->followersCount;
    }

    public function setFollowersCount(int $followersCount): self
    {
        $this->followersCount = $followersCount;

        return $this;
    }

    public function getFollowsCount(): int
    {
        return $this->followsCount;
    }

    public function setFollowsCount(int $followsCount): self
    {
        $this->followsCount = $followsCount;

        return $this;
    }

    public function getMediaCount(): int
    {
        return $this->mediaCount;
    }

    public function setMediaCount(int $mediaCount): self
    {
        $this->mediaCount = $mediaCount;

        return $this;
    }

    public function getLanguageUid(): ?int
    {
        return $this->_languageUid;
    }

    /**
     * @param int<-1, max>|null $languageUid
     */
    public function setLanguageUid(?int $languageUid): self
    {
        $this->_languageUid = $languageUid;

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

    /**
     * @param array<string, mixed> $data
     */
    public function updateFromArray(array $data): self
    {
        foreach ($data as $key => $value) {
            $setter = sprintf('set%s', ucfirst($this->snakeToCamel($key)));

            if (!method_exists($this, $setter)) {
                continue;
            }

            $this->$setter($value);
        }

        return $this;
    }

    private function snakeToCamel(string $input): string
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $input))));
    }
}
