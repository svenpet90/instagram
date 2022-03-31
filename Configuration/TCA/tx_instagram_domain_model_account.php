<?php

return [
    'ctrl' => [
        'title' => 'LLL:EXT:instagram/Resources/Private/Language/locallang_db.xlf:tx_instagram_domain_model_account',
        'label' => 'username',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
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
        'searchFields' => 'username,userid,posts,lastupdate',
        'iconfile' => 'EXT:instagram/Resources/Public/Icons/tx_instagram_domain_model_account.gif',
    ],
    'interface' => [],
    'types' => [
        '1' => [
            'showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, longlivedaccesstoken, username, userid, posts, lastupdate, --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access, starttime, endtime',
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
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
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
                'foreign_table' => 'tx_instagram_domain_model_account',
                'foreign_table_where' => 'AND tx_instagram_domain_model_account.pid=###CURRENT_PID### AND tx_instagram_domain_model_account.sys_language_uid IN (-1,0)',
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
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
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
        'userid' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:instagram/Resources/Private/Language/locallang_db.xlf:tx_instagram_domain_model_account.userid',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'username' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:instagram/Resources/Private/Language/locallang_db.xlf:tx_instagram_domain_model_account.username',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'lastupdate' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:instagram/Resources/Private/Language/locallang_db.xlf:tx_instagram_domain_model_account.lastupdate',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'integer',
            ],
        ],
        'longlivedaccesstoken' => [
            'exclude' => 0,
            'label' => 'Long Lived Access Token',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_instagram_domain_model_longlivedaccesstoken',
                'maxitems' => 1,
                'overrideChildTca' => [
                    'types' => [
                        '0' => [
                            'showitem' => 'userid, token, type, expiresat',
                        ],
                    ],
                ],
            ],
        ],
        'posts' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:instagram/Resources/Private/Language/locallang_db.xlf:tx_instagram_domain_model_account.posts',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_instagram_domain_model_post',
                'foreign_field' => 'account',
            ],
        ],
        'pid' => [
            'config' => [
                'type' => 'passthrough',
            ]
        ],
    ],
];
