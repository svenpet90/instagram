<?php

if (!isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['features']['instagram.tokenGeneratorBeModule'])) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['features']['instagram.tokenGeneratorBeModule'] = true;
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Instagram',
    'List',
    [
        \SvenPetersen\Instagram\Controller\PostController::class => 'list, show',
    ],
    // non-cacheable actions
    [
        \SvenPetersen\Instagram\Controller\PostController::class => '',
    ],
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Instagram',
    'Show',
    [
        \SvenPetersen\Instagram\Controller\PostController::class => 'show',
    ],
    // non-cacheable actions
    [
        \SvenPetersen\Instagram\Controller\PostController::class => '',
    ],
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
);
