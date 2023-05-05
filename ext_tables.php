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
