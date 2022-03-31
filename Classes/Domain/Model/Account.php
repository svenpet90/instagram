<?php

declare(strict_types=1);

namespace SvenPetersen\Instagram\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

final class Account extends AbstractEntity
{
    /**
     * @var int
     */
    protected $_languageUid;

    protected string $userid = '';

    protected string $username = '';

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\SvenPetersen\Instagram\Domain\Model\Post>
     */
    protected ?ObjectStorage $posts = null;

    protected int $lastupdate = 0;

    /**
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade("persist")
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade("remove")
     */
    protected ?Longlivedaccesstoken $longlivedaccesstoken = null;

    public function __construct(string $userId)
    {
        $this->userid = $userId;
        $this->posts = new ObjectStorage();
    }

    public function setSysLanguageUid(int $_languageUid): self
    {
        $this->_languageUid = $_languageUid;

        return $this;
    }

    public function getSysLanguageUid(): int
    {
        return $this->_languageUid;
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

    public function getUserid(): string
    {
        return $this->userid;
    }

    public function addPost(Post $post): self
    {
        $this->posts->attach($post);

        return $this;
    }

    /**
     * @param Post $postToRemove The Post to be removed
     */
    public function removePost(Post $postToRemove): self
    {
        $this->posts->detach($postToRemove);

        return $this;
    }

    public function getPosts(): ObjectStorage
    {
        return $this->posts;
    }

    public function setPosts(ObjectStorage $posts): self
    {
        $this->posts = $posts;

        return $this;
    }

    public function getLastupdate(): int
    {
        return $this->lastupdate;
    }

    public function setLastupdate(int $lastupdate): self
    {
        $this->lastupdate = $lastupdate;

        return $this;
    }

    public function getLonglivedaccesstoken()
    {
        return $this->longlivedaccesstoken;
    }

    public function setLonglivedaccesstoken(Longlivedaccesstoken $longlivedaccesstoken): self
    {
        $this->longlivedaccesstoken = $longlivedaccesstoken;

        return $this;
    }
}
