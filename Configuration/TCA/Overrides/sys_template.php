<?php

if (! defined('TYPO3')) {
    die('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'instagram',
    'Configuration/TypoScript',
    'TYPO3 Instagram Client'
);
