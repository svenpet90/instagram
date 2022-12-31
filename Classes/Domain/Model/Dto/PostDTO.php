<?php

declare(strict_types=1);

namespace SvenPetersen\Instagram\Domain\Model\Dto;

class PostDTO
{
    private string $id;

    private string $caption;

    private string $mediaUrl;

    private string $permalink;

    private ?\DateTimeImmutable $timestamp;

    private string $username;

    private string $thumbnailUrl;

    private string $mediaType;

    /**
     * @var PostDTO[]
     */
    private array $children;

    public function __construct(
        string $id,
        string $caption,
        string $mediaUrl,
        string $permalink,
        \DateTimeImmutable $timestamp,
        string $username,
        string $thumbnailUrl,
        string $mediaType
    ) {
        $this->id = $id;
        $this->caption = $caption;
        $this->mediaUrl = $mediaUrl;
        $this->permalink = $permalink;
        $this->timestamp = $timestamp;
        $this->username = $username;
        $this->thumbnailUrl = $thumbnailUrl;
        $this->mediaType = $mediaType;
    }

    public function getCaption(): string
    {
        return $this->caption;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getMediaUrl(): string
    {
        return $this->mediaUrl;
    }

    public function getPermalink(): string
    {
        return $this->permalink;
    }

    public function getTimestamp(): ?\DateTimeImmutable
    {
        return $this->timestamp;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getThumbnailUrl(): string
    {
        return $this->thumbnailUrl;
    }

    public function getMediaType(): string
    {
        return $this->mediaType;
    }

    /**
     * @param PostDTO[] $children
     */
    public function setChildren(array $children): self
    {
        $this->children = $children;

        return $this;
    }

    /**
     * @return PostDTO[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }
}
