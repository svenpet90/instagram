<?php

return [
    'ctrl' => [
        'title' => 'LLL:EXT:instagram/Resources/Private/Language/locallang_db.xlf:tx_instagram_domain_model_post',
        'label' => 'caption',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'default_sortby' => 'ORDER BY posted_at DESC',
        'dividers2tabs' => true,
        'versioningWS' => 2,
        'versioning_followPages' => true,
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'media_type,caption,images,videos,posted_at,instagramid,link,lastupdate,feed',
        'iconfile' => 'EXT:instagram/Resources/Public/Icons/tx_instagram_domain_model_post.gif',
    ],
    'interface' => [],
    'types' => [
        '1' => [
            'showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, feed, media_type, caption, images, videos, posted_at, instagramid, hashtags, link, lastupdate, --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access, starttime, endtime',
        ],
    ],
    'palettes' => [
        '1' => [
            'showitem' => '',
        ],
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'sys_language',
                'foreign_table_where' => 'ORDER BY sys_language.title',
                'items' => [
                    ['LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages', -1],
                    ['LLL:EXT:lang/locallang_general.xlf:LGL.default_value', 0],
                ],
            ],
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_instagram_domain_model_post',
                'foreign_table_where' => 'AND tx_instagram_domain_model_post.pid=###CURRENT_PID### AND tx_instagram_domain_model_post.sys_language_uid IN (-1,0)',
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        't3ver_label' => [
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.versionLabel',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
            ],
        ],
        'hidden' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
            ],
        ],
        'starttime' => [
            'exclude' => 1,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'input',
                'size' => 13,
                'max' => 20,
                'eval' => 'datetime',
                'checkbox' => 0,
                'default' => 0,
                'range' => [
                    'lower' => mktime(0, 0, 0, (int)date('m'), (int)date('d'), (int)date('Y')),
                ],
            ],
        ],
        'endtime' => [
            'exclude' => 1,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'input',
                'size' => 13,
                'max' => 20,
                'eval' => 'datetime',
                'checkbox' => 0,
                'default' => 0,
                'range' => [
                    'lower' => mktime(0, 0, 0, (int)date('m'), (int)date('d'), (int)date('Y')),
                ],
            ],
        ],
        'caption' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:instagram/Resources/Private/Language/locallang_db.xlf:tx_instagram_domain_model_post.caption',
            'config' => [
                'type' => 'text',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'posted_at' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:instagram/Resources/Private/Language/locallang_db.xlf:tx_instagram_domain_model_post.posted_at',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime',
            ],
        ],
        'instagramid' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:instagram/Resources/Private/Language/locallang_db.xlf:tx_instagram_domain_model_post.instagramid',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'hashtags' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:instagram/Resources/Private/Language/locallang_db.xlf:tx_instagram_domain_model_post.hashtags',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'link' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:instagram/Resources/Private/Language/locallang_db.xlf:tx_instagram_domain_model_post.link',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'media_type' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:instagram/Resources/Private/Language/locallang_db.xlf:tx_instagram_domain_model_post.media_type',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'readOnly' => 1,
            ],
        ],
        'lastupdate' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:instagram/Resources/Private/Language/locallang_db.xlf:tx_instagram_domain_model_post.lastupdate',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime',
            ],
        ],
        'videos' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:instagram/Resources/Private/Language/locallang_db.xlf:tx_instagram_domain_model_post.videos',
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
                'videos',
                [
                    'appearance' => [
                        'createNewRelationLinkTitle' => 'LLL:EXT:cms/locallang_ttc.xlf:images.addFileReference',
                    ],
                ],
                'gif,jpg,jpeg,tif,tiff,bmp,pcx,tga,png,pdf,ai,svg,mp4'
            ),
        ],
        'feed' => [
            'label' => 'LLL:EXT:instagram/Resources/Private/Language/locallang_db.xlf:tx_instagram_domain_model_post.feed',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_instagram_domain_model_feed',
                'foreign_table_where' => 'ORDER BY tx_instagram_domain_model_feed.username',
            ],
        ],
        'images' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:instagram/Resources/Private/Language/locallang_db.xlf:tx_instagram_domain_model_post.images',
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
                'images',
                [
                    'appearance' => [
                        'createNewRelationLinkTitle' => 'LLL:EXT:cms/locallang_ttc.xlf:images.addFileReference',
                    ],
                    'foreign_types' => [
                        '0' => [
                            'showitem' => '
							--palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
							--palette--;;filePalette',
                        ],
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_TEXT => [
                            'showitem' => '
							--palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
							--palette--;;filePalette',
                        ],
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => [
                            'showitem' => '
							--palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
							--palette--;;filePalette',
                        ],
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_AUDIO => [
                            'showitem' => '
							--palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
							--palette--;;filePalette',
                        ],
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_VIDEO => [
                            'showitem' => '
							--palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
							--palette--;;filePalette',
                        ],
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_APPLICATION => [
                            'showitem' => '
							--palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
							--palette--;;filePalette',
                        ],
                    ],
                    'maxitems' => 9999,
                ],
                $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
            ),
        ],
    ],
];
