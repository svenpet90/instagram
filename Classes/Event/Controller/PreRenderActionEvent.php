<?php

declare(strict_types=1);

/*
 * This file is part of the Extension "instagram" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace SvenPetersen\Instagram\Event\Controller;

use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;

/**
 * This event can be used to modify the view object before it is rendered. E.g. add additional variables.
 */
class PreRenderActionEvent
{
    private ViewInterface $view;

    private string $action;

    public function __construct(ViewInterface $view, string $action)
    {
        $this->view = $view;
        $this->action = $action;
    }

    public function getView(): ViewInterface
    {
        return $this->view;
    }

    public function setView(ViewInterface $view): void
    {
        $this->view = $view;
    }

    public function getAction(): string
    {
        return $this->action;
    }
}
