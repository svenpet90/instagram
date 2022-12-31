<?php

/*
 * This file is part of the "instagram" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */
declare(strict_types=1);

namespace SvenPetersen\Instagram\Tests;

use TYPO3\CMS\Core\Core\Environment;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

abstract class BaseTest extends FunctionalTestCase
{
    protected $coreExtensionsToLoad = [
        'install',
    ];

    protected $testExtensionsToLoad = [
        'typo3conf/ext/instagram',
    ];

    public function setUp(): void
    {
        parent::setUp();

        $this->importDataSet(sprintf('%s/Fixtures/pages.xml', __DIR__));
        $this->importDataSet(sprintf('%s/Fixtures/content.xml', __DIR__));
        $this->importDataSet(sprintf('%s/Fixtures/feeds.xml', __DIR__));
        $this->importDataSet(sprintf('%s/Fixtures/posts.xml', __DIR__));

        $this->setUpFrontendRootPage(
            1,
            [
                'constants' => [
                    'EXT:instagram/Configuration/TypoScript/constants.typoscript',
                ],
                'setup' => [
                    'EXT:instagram/Configuration/TypoScript/setup.typoscript',
                ],
            ]
        );

        $siteConfigDir = sprintf('%s/sites/instagram', Environment::getConfigPath());
        mkdir($siteConfigDir, 0777, true);

        $configContents = "rootPageId: 1\nbase: /\nbaseVariants: { }\nlanguages: { }\nroutes: { }\n";
        file_put_contents(sprintf('%s/config.yaml', $siteConfigDir), $configContents);
    }
}
