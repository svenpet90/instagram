<?php

declare(strict_types=1);

namespace SvenPetersen\Instagram\Hooks;

/**
 * This file is part of the "instagram" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use SvenPetersen\Instagram\Utility\TemplateLayoutUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility as BackendUtilityCore;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ItemsProcFunc
{
    protected TemplateLayoutUtility $templateLayoutsUtility;

    public function __construct()
    {
        /** @var TemplateLayoutUtility $templateLayoutsUtility */
        $templateLayoutsUtility = GeneralUtility::makeInstance(TemplateLayoutUtility::class);
        $this->templateLayoutsUtility = $templateLayoutsUtility;
    }

    /**
     * Itemsproc function to extend the selection of templateLayouts in the plugin
     *
     * @param array &$config configuration array
     */
    public function user_templateLayout(array &$config): void
    {
        $currentColPos = $config['flexParentDatabaseRow']['colPos'];
        $pageId = $this->getPageId($config['flexParentDatabaseRow']['pid']);

        if ($pageId > 0) {
            $templateLayouts = $this->templateLayoutsUtility->getAvailableTemplateLayouts($pageId);
            $templateLayouts = $this->reduceTemplateLayouts($templateLayouts, $currentColPos);

            foreach ($templateLayouts as $layout) {
                $additionalLayout = [
                    htmlspecialchars($this->getLanguageService()->sL($layout[0])),
                    $layout[1],
                ];

                array_push($config['items'], $additionalLayout);
            }
        }
    }

    /**
     * Reduce the template layouts by the ones that are not allowed in given colPos
     *
     * @param array $templateLayouts
     * @param int $currentColPos
     * @return array
     */
    protected function reduceTemplateLayouts($templateLayouts, $currentColPos): array
    {
        $currentColPos = (int)$currentColPos;
        $restrictions = [];
        $allLayouts = [];

        foreach ($templateLayouts as $key => $layout) {
            if (is_array($layout[0])) {
                if (isset($layout[0]['allowedColPos']) && str_ends_with((string)$layout[1], '.')) {
                    $layoutKey = substr($layout[1], 0, -1);
                    $restrictions[$layoutKey] = GeneralUtility::intExplode(',', $layout[0]['allowedColPos'], true);
                }
            } else {
                $allLayouts[$key] = $layout;
            }
        }

        if (!empty($restrictions)) {
            foreach ($restrictions as $restrictedIdentifier => $restrictedColPosList) {
                if (!in_array($currentColPos, $restrictedColPosList, true)) {
                    unset($allLayouts[$restrictedIdentifier]);
                }
            }
        }

        return $allLayouts;
    }

    /**
     * Get all languages
     *
     * @return array
     */
    protected function getAllLanguages(): array
    {
        $versionInformation = GeneralUtility::makeInstance(Typo3Version::class);

        if ($versionInformation->getMajorVersion() === 10) {
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
                ->getQueryBuilderForTable('sys_language');

            return $queryBuilder->select('*')
                ->from('sys_language')
                ->orderBy('sorting')
                ->execute()
                ->fetchAll();
        }

        $siteLanguages = [];

        foreach (GeneralUtility::makeInstance(SiteFinder::class)->getAllSites() as $site) {
            foreach ($site->getAllLanguages() as $languageId => $language) {
                if (!isset($siteLanguages[$languageId])) {
                    $siteLanguages[$languageId] = [
                        'uid' => $languageId,
                        'title' => $language->getTitle(),
                    ];
                }
            }
        }

        return $siteLanguages;
    }

    /**
     * Get page id, if negative, then it is a "after record"
     *
     * @param int $pid
     * @return int
     */
    protected function getPageId($pid): int
    {
        $pid = (int)$pid;

        if ($pid > 0) {
            return $pid;
        }

        $row = BackendUtilityCore::getRecord('tt_content', abs($pid), 'uid,pid');

        return $row['pid'];
    }

    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
