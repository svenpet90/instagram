<?php

declare(strict_types=1);

namespace SvenPetersen\Instagram\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

final class Post extends AbstractEntity
{
    protected $_languageUid;

    protected string $text = '';

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    protected ?ObjectStorage $images = null;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    protected ?ObjectStorage $videos = null;

    protected ?int $createdtime = null;

    protected ?string $instagramid = null;

    protected ?string $tags = null;

    protected ?string $link = null;

    protected ?string $type = null;

    protected ?int $lastupdate = null;

    protected ?Account $account = null;

    public function setSysLanguageUid(int $_languageUid): self
    {
        $this->_languageUid = $_languageUid;

        return $this;
    }

    public function getSysLanguageUid(): int
    {
        return $this->_languageUid;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(?string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getCreatedtime(): ?int
    {
        return $this->createdtime;
    }

    public function setCreatedtime(?int $createdtime): self
    {
        $this->createdtime = $createdtime;

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

    public function getTags(): string
    {
        return $this->tags;
    }

    public function setTags(string $tags): self
    {
        $this->tags = $tags;

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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getLastupdate(): ?int
    {
        return $this->lastupdate;
    }

    public function setLastupdate(int $lastupdate): self
    {
        $this->lastupdate = $lastupdate;

        return $this;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference> $images
     */
    public function getImages(): ?ObjectStorage
    {
        return $this->images;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference> $images
     */
    public function setImage(ObjectStorage $images): self
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

    public function getAccount(): Account
    {
        return $this->account;
    }

    public function setAccount(Account $account): self
    {
        $this->account = $account;

        return $this;
    }
}
