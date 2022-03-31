<?php

if (! defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
    'tx_instagram_domain_model_post',
    'EXT:instagram/Resources/Private/Language/locallang_csh_tx_instagram_domain_model_post.xlf'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_instagram_domain_model_post');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
    'tx_instagram_domain_model_longlivedaccesstoken',
    'EXT:instagram/Resources/Private/Language/locallang_csh_tx_instagram_domain_model_longlivedaccesstoken.xlf'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_instagram_domain_model_longlivedaccesstoken');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
    'tx_instagram_domain_model_account',
    'EXT:instagram/Resources/Private/Language/locallang_csh_tx_instagram_domain_model_account.xlf'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_instagram_domain_model_account');

if (TYPO3) {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'SvenPetersen.Instagram',
        'tools',
        'mod1',
        '',
        [
            \SvenPetersen\Instagram\Controller\SetupController::class => 'stepone, steptwo, stepthree',
        ],
        [
            'access' => 'user,group',
            'icon' => 'EXT:instagram/Resources/Public/Icons/instagram.jpg',
            'labels' => 'LLL:EXT:instagram/Resources/Private/Language/locallang_db.xml:instagram',
        ]
    );
}
