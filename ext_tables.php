<?php

if (! defined('TYPO3')) {
    die('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
    'tx_instagram_domain_model_post',
    'EXT:instagram/Resources/Private/Language/locallang_csh_tx_instagram_domain_model_post.xlf'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_instagram_domain_model_post');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
    'tx_instagram_domain_model_feed',
    'EXT:instagram/Resources/Private/Language/locallang_csh_tx_instagram_domain_model_feed.xlf'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_instagram_domain_model_feed');

if (TYPO3) {
    if (TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(TYPO3\CMS\Core\Configuration\Features::class)->isFeatureEnabled('instagram.tokenGeneratorBeModule')) {
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
            'SvenPetersen.Instagram',
            'tools',
            'accessTokenGenerator',
            '',
            [
                \SvenPetersen\Instagram\Controller\TokenGeneratorController::class => 'stepOne, stepTwo, stepThree',
            ],
            [
                'access' => 'user,group',
                'icon' => 'EXT:instagram/Resources/Public/Icons/instagram.jpg',
                'labels' => 'LLL:EXT:instagram/Resources/Private/Language/locallang_db.xml:instagram',
            ]
        );
    }
}
