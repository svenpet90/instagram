<?php

declare(strict_types=1);

namespace SvenPetersen\Instagram\Event\Post;

use SvenPetersen\Instagram\Domain\Model\Post;

class PrePersistPostEvent
{
    private Post $post;

    private string $action;

    public function __construct(Post $post, string $action)
    {
        $this->post = $post;
        $this->action = $action;
    }

    public function getPost(): Post
    {
        return $this->post;
    }

    public function setPost(Post $post): self
    {
        $this->post = $post;

        return $this;
    }

    public function getAction(): string
    {
        return $this->action;
    }
}
