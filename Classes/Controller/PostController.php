<?php

declare(strict_types=1);

/*
 * This file is part of the Extension "instagram" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace SvenPetersen\Instagram\Controller;

use SvenPetersen\Instagram\Domain\Model\Post;
use SvenPetersen\Instagram\Domain\Repository\PostRepository;
use SvenPetersen\Instagram\Event\Controller\PreRenderActionEvent;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class PostController extends ActionController
{
    public function listAction(): void
    {
        /** @var PostRepository $postRepository */
        $postRepository = GeneralUtility::makeInstance(PostRepository::class);
        $posts = $postRepository->findBySettings($this->settings);

        $this->view->assign('posts', $posts);

        /** @var PreRenderActionEvent $event */
        $event = $this->eventDispatcher->dispatch(new PreRenderActionEvent($this->view, __METHOD__));

        $this->view = $event->view;
    }

    public function showAction(Post $post): void
    {
        $this->view->assign('post', $post);

        /** @var PreRenderActionEvent $event */
        $event = $this->eventDispatcher->dispatch(new PreRenderActionEvent($this->view, __METHOD__));

        $this->view = $event->view;
    }
}
