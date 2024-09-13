<?php

return [
    'ctrl' => [
        'title' => 'LLL:EXT:instagram/Resources/Private/Language/locallang_db.xlf:tx_instagram_domain_model_feed',
        'label' => 'username',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'dividers2tabs' => true,
        'versioningWS' => 2,
        'versioning_followPages' => true,
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'security' => [
            'ignorePageTypeRestriction' => true,
        ],
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'username,token,token_type,expires_at,user_id,posts',
        'iconfile' => 'EXT:instagram/Resources/Public/Icons/tx_instagram_domain_model_feed.gif',
    ],
    'interface' => [],
    'types' => [
        '1' => [
            'showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, id, user_id, username, name, account_type, profile_picture_url, followers_count, follows_count, media_count, token, expires_at, token_type, posts, --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access, starttime, endtime',
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
                'type' => 'language',
            ],
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'label' => '',
                        'value' => 0,
                    ],
                ],
                'foreign_table' => 'tx_instagram_domain_model_feed',
                'foreign_table_where' => 'AND tx_instagram_domain_model_feed.pid=###CURRENT_PID### AND tx_instagram_domain_model_feed.sys_language_uid IN (-1,0)',
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
        'id' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:instagram/Resources/Private/Language/locallang_db.xlf:tx_instagram_domain_model_feed.id',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'user_id' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:instagram/Resources/Private/Language/locallang_db.xlf:tx_instagram_domain_model_feed.user_id',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'username' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:instagram/Resources/Private/Language/locallang_db.xlf:tx_instagram_domain_model_feed.username',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'name' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:instagram/Resources/Private/Language/locallang_db.xlf:tx_instagram_domain_model_feed.name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'account_type' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:instagram/Resources/Private/Language/locallang_db.xlf:tx_instagram_domain_model_feed.account_type',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'profile_picture_url' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:instagram/Resources/Private/Language/locallang_db.xlf:tx_instagram_domain_model_feed.profile_picture_url',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'followers_count' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:instagram/Resources/Private/Language/locallang_db.xlf:tx_instagram_domain_model_feed.followers_count',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'follows_count' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:instagram/Resources/Private/Language/locallang_db.xlf:tx_instagram_domain_model_feed.followers_count',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'media_count' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:instagram/Resources/Private/Language/locallang_db.xlf:tx_instagram_domain_model_feed.media_count',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'token' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:instagram/Resources/Private/Language/locallang_db.xlf:tx_instagram_domain_model_feed.token',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'expires_at' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:instagram/Resources/Private/Language/locallang_db.xlf:tx_instagram_domain_model_feed.expires_at',
            'config' => [
                'type' => 'datetime',
                'format' => 'datetime',
            ],
        ],
        'token_type' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:instagram/Resources/Private/Language/locallang_db.xlf:tx_instagram_domain_model_feed.token_type',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'posts' => [
            'label' => 'LLL:EXT:instagram/Resources/Private/Language/locallang_db.xlf:tx_instagram_domain_model_feed.posts',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_instagram_domain_model_post',
                'foreign_field' => 'feed',
            ],

        ],
    ],
];
