<?php

declare(strict_types=1);

/*
 * This file is part of the Extension "instagram" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace SvenPetersen\Instagram\Controller;

use Psr\Http\Message\ResponseInterface;
use SvenPetersen\Instagram\Domain\Model\Post;
use SvenPetersen\Instagram\Domain\Repository\PostRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class PostController extends ActionController
{
    public function listAction(): ResponseInterface
    {
        /** @var PostRepository $postRepository */
        $postRepository = GeneralUtility::makeInstance(PostRepository::class);
        $posts = $postRepository->findBySettings($this->settings);

        $this->view->assignMultiple([
            'cObjectData' => $this->request->getAttribute('currentContentObject')->data,
            'posts' => $posts,
        ]);

        return $this->htmlResponse();
    }

    public function showAction(Post $post): ResponseInterface
    {
        $this->view->assign('post', $post);

        return $this->htmlResponse();
    }
}
