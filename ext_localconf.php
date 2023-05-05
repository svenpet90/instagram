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
    ]
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
    ]
);

/** @var \TYPO3\CMS\Core\Information\Typo3Version $versionInformation */
$versionInformation = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Information\Typo3Version::class);

if ($versionInformation->getMajorVersion() === 10) {
    $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
        \TYPO3\CMS\Core\Imaging\IconRegistry::class
    );
    $iconRegistry->registerIcon(
        'actions-brand-instagram',
        \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        ['source' => 'EXT:instagram/Resources/Public/Icons/actions-brand-instagram.svg']
    );
}
