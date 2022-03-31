<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Instagram',
    'description' => 'This extension provides scheduler tasks to import the contents of an instagram feed',
    'category' => 'plugin',
    'author' => 'Sven Petersen',
    'author_email' => 'info@svenharders.de',
    'state' => 'stable',
    'internal' => '',
    'uploadfolder' => '1',
    'clearCacheOnLoad' => 0,
    'version' => '2.0.1',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-11.5.99',
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
];
