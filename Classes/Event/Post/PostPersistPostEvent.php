<?php

declare(strict_types=1);

namespace SvenPetersen\Instagram\Event\Post;

use SvenPetersen\Instagram\Domain\Model\Post;

class PostPersistPostEvent
{
    private Post $post;

    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    public function getPost(): Post
    {
        return $this->post;
    }
}
