<?php

if (! defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'SvenPetersen.Instagram',
    'Pi1',
    [
        \SvenPetersen\Instagram\Controller\PostController::class => 'listByAccounts',
    ],
    [
    ]
);
