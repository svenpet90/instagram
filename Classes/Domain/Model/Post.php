<?php

declare(strict_types=1);

namespace SvenPetersen\Instagram\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class Post extends AbstractEntity
{
    public const MEDIA_TYPE_IMAGE = 'IMAGE';

    public const MEDIA_TYPE_CAROUSEL_ALBUM = 'CAROUSEL_ALBUM';

    public const MEDIA_TYPE_VIDEO = 'VIDEO';

    /**
     * @var int
     */
    protected $_languageUid;

    protected ?string $caption = null;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    protected ?ObjectStorage $images = null;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    protected ?ObjectStorage $videos = null;

    protected ?\DateTimeImmutable $postedAt = null;

    protected ?string $instagramid = null;

    protected ?string $hashtags = null;

    protected ?string $link = null;

    protected ?string $mediaType = null;

    protected ?\DateTimeImmutable $lastupdate = null;

    protected ?Feed $feed = null;

    public function setSysLanguageUid(int $_languageUid): self
    {
        $this->_languageUid = $_languageUid;

        return $this;
    }

    public function getSysLanguageUid(): int
    {
        return $this->_languageUid;
    }

    public function getCaption(): string
    {
        return $this->caption;
    }

    public function setCaption(?string $caption): self
    {
        $this->caption = $caption;

        return $this;
    }

    public function getPostedAt(): ?\DateTimeImmutable
    {
        return $this->postedAt;
    }

    public function setPostedAt(?\DateTimeImmutable $postedAt): self
    {
        $this->postedAt = $postedAt;

        return $this;
    }

    public function getInstagramid(): string
    {
        return $this->instagramid;
    }

    public function setInstagramid(?string $instagramid): self
    {
        $this->instagramid = $instagramid;

        return $this;
    }

    public function getHashtags(): string
    {
        return $this->hashtags;
    }

    public function setHashtags(string $hashtags): self
    {
        $this->hashtags = $hashtags;

        return $this;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function setLink(string $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function getMediaType(): ?string
    {
        return $this->mediaType;
    }

    public function setMediaType(string $mediaType): self
    {
        $this->mediaType = $mediaType;

        return $this;
    }

    public function getLastupdate(): ?\DateTimeImmutable
    {
        return $this->lastupdate;
    }

    public function setLastupdate(\DateTimeImmutable $lastupdate): self
    {
        $this->lastupdate = $lastupdate;

        return $this;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference> $image
     */
    public function getImages(): ?ObjectStorage
    {
        return $this->images;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference> $images
     */
    public function setImages(ObjectStorage $images): self
    {
        $this->images = $images;

        return $this;
    }

    public function getVideos(): ?ObjectStorage
    {
        return $this->videos;
    }

    public function setVideos($videos): self
    {
        $this->videos = $videos;

        return $this;
    }

    public function getFeed(): ?FeedInterface
    {
        return $this->feed;
    }

    public function setFeed(?FeedInterface $feed): self
    {
        $this->feed = $feed;

        return $this;
    }
}
