<?php

declare(strict_types=1);

namespace SvenPetersen\Instagram\Controller;

use SvenPetersen\Instagram\Domain\Model\Post;
use SvenPetersen\Instagram\Domain\Repository\PostRepository;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class PostController extends ActionController
{
    private PostRepository $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function listAction(): void
    {
        $posts = $this->postRepository->findAll();

        $this->view->assign('posts', $posts);
    }

    public function showAction(Post $post): void
    {
        $this->view->assign('post', $post);
    }
}
