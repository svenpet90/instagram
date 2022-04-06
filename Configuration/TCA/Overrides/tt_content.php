<?php

if (! defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'SvenPetersen.Instagram',
    'Pi1',
    'Instagram: List Posts'
);

$pluginSignature = str_replace('_', '', 'instagram') . '_pi1';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    $pluginSignature,
    'FILE:EXT:instagram/Configuration/FlexForms/Pi1.xml'
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'SvenPetersen.Instagram',
    'Pi2',
    'Instagram: Show single Post'
);
