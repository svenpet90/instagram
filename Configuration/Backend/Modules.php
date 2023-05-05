<?php

use SvenPetersen\Instagram\Controller\TokenGeneratorController;

if (TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(TYPO3\CMS\Core\Configuration\Features::class)
    ->isFeatureEnabled('instagram.tokenGeneratorBeModule')
) {
    return [
        'admin_instagram' => [
            'parent' => 'system',
            'position' => ['top'],
            'access' => 'user,group',
            'workspaces' => 'live',
            'path' => '/module/system/instagram',
            'icon' => 'EXT:instagram/Resources/Public/Icons/instagram.jpg',
            'labels' => 'LLL:EXT:instagram/Resources/Private/Language/locallang_db.xml:instagram',
            'extensionName' => 'Examples',
            'controllerActions' => [
                TokenGeneratorController::class => [
                    'stepOne', 'stepTwo', 'stepThree',
                ],
            ],
        ],
    ];
}
