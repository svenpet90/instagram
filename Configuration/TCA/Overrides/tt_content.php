<?php

if (!defined('TYPO3')) {
    die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'Instagram',
    'List',
    'Instagram: List of posts',
    'actions-brand-instagram'
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'Instagram',
    'Show',
    'Instagram: Show single post'
);

$flexformMappings = [
    ['instagram_list', 'FILE:EXT:instagram/Configuration/FlexForms/List.xml'],
];

foreach ($flexformMappings as $map) {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
        'tt_content',
        '--div--;Configuration,pi_flexform,pages,recursive,pages,recursive',
        $map[0],
        'after:subheader',
    );

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
        '*',
        $map[1],
        $map[0],
    );
}
