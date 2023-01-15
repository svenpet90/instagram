<?php

declare(strict_types=1);

namespace SvenPetersen\Instagram\Utility;

/**
 * This file is part of the "instagram" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class TemplateLayoutUtility implements SingletonInterface
{
    /**
     * @return array<string, array<int, string>>
     */
    public function getAvailableTemplateLayouts(int $pageUid): array
    {
        $templateLayouts = [];

        // Check if the layouts are extended by ext_tables
        if (isset($GLOBALS['TYPO3_CONF_VARS']['EXT']['instagram']['templateLayouts'])
            && is_array($GLOBALS['TYPO3_CONF_VARS']['EXT']['instagram']['templateLayouts'])
        ) {
            $templateLayouts = $GLOBALS['TYPO3_CONF_VARS']['EXT']['instagram']['templateLayouts'];
        }

        // Add TsConfig values
        foreach ($this->getTemplateLayoutsFromTsConfig($pageUid) as $templateKey => $title) {
            if (is_string($title) && str_starts_with($title, '--div--')) {
                $optGroupParts = GeneralUtility::trimExplode(',', $title, true, 2);
                $title = $optGroupParts[1];
                $templateKey = $optGroupParts[0];
            }

            $templateLayouts[] = [$title, $templateKey];
        }

        return $templateLayouts;
    }

    /**
     * @param int $pageUid
     * @return array<string|int,string>
     */
    protected function getTemplateLayoutsFromTsConfig(int $pageUid): array
    {
        $templateLayouts = [];

        $pagesTsConfig = BackendUtility::getPagesTSconfig($pageUid);

        if (isset($pagesTsConfig['tx_instagram.']['templateLayouts.']) && is_array($pagesTsConfig['tx_instagram.']['templateLayouts.'])) {
            $templateLayouts = $pagesTsConfig['tx_instagram.']['templateLayouts.'];
        }

        return $templateLayouts;
    }
}
