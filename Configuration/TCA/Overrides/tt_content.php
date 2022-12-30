<?php

if (! defined('TYPO3')) {
    die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'SvenPetersen.Instagram',
    'List',
    'Instagram'
);

$pluginSignature = 'instagram_list';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    $pluginSignature,
    'FILE:EXT:instagram/Configuration/FlexForms/List.xml'
);
