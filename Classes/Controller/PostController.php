<?php

declare(strict_types=1);

/*
 * This file is part of the Extension "instagram" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace SvenPetersen\Instagram\Controller;

use GeorgRinger\NumberedPagination\NumberedPagination;
use Psr\Http\Message\ResponseInterface;
use SvenPetersen\Instagram\Domain\Model\Post;
use SvenPetersen\Instagram\Domain\Repository\PostRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Pagination\QueryResultPaginator;

class PostController extends ActionController
{
    public function listAction(): ResponseInterface
    {
        /** @var PostRepository $postRepository */
        $postRepository = GeneralUtility::makeInstance(PostRepository::class);
        $posts = $postRepository->findBySettings($this->settings);

        if ($this->settings['showPagination']) {
            $itemsPerPage = (int)$this->settings['pagination']['itemsPerPage'];
            $maximumLinks = (int)$this->settings['pagination']['maxLinks'];
            $currentPage = $this->request->hasArgument('currentPage') ? (int)$this->request->getArgument('currentPage') : 1;
            $paginator = new QueryResultPaginator($posts, $currentPage, $itemsPerPage);
            $pagination = new NumberedPagination($paginator, $maximumLinks);
            $this->view->assign('pagination', [
                'paginator' => $paginator,
                'pagination' => $pagination,
            ]);

            return $this->htmlResponse();
        }
        $this->view->assign('posts', $posts);

        return $this->htmlResponse();
    }

    public function showAction(Post $post): ResponseInterface
    {
        $this->view->assign('post', $post);

        return $this->htmlResponse();
    }
}
