<?php

declare(strict_types=1);

namespace SvenPetersen\Instagram\Tests\Unit\Service;

use SvenPetersen\Instagram\Domain\Model\Feed;
use SvenPetersen\Instagram\Domain\Repository\FeedRepository;
use SvenPetersen\Instagram\Service\AccessTokenRefresher;
use SvenPetersen\Instagram\Service\AccessTokenService;
use SvenPetersen\Instagram\Tests\BaseTest;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;

class AccessTokenRefresherTest extends BaseTest
{
    private ?AccessTokenRefresher $accessTokenRefresher = null;

    private AccessTokenService $accessTokenService;

    public function setUp(): void
    {
        parent::setUp();

        $feedRepoMock = $this->createMock(FeedRepository::class);
        $persistenceManagerInterfaceMock = $this->createMock(PersistenceManagerInterface::class);
        $this->accessTokenService = $this->createMock(AccessTokenService::class);

        $this->accessTokenRefresher = new AccessTokenRefresher($feedRepoMock, $persistenceManagerInterfaceMock, $this->accessTokenService);
    }

    /**
     * @dataProvider getRefreshAccessTokenTests
     */
    public function testRefreshAccessToken(Feed $feed, int $expectedApiCalls, string $expectedToken): void
    {
        $this->accessTokenService
            ->expects(self::exactly($expectedApiCalls))
            ->method('refreshAccessToken')
            ->willReturn([
                'access_token' => 'newAccessToken',
                'expires_in' => 123456789,
                'token_type' => 'bearer',
            ]);

        $result = $this->accessTokenRefresher->refreshAccessToken($feed);

        self::assertInstanceOf(Feed::class, $result);
        self::assertEquals($expectedToken, $feed->getToken());
        self::assertEquals('bearer', $feed->getTokenType());
    }

    /**
     * @return mixed[]
     */
    public function getRefreshAccessTokenTests(): array
    {
        return [
            [(new Feed())
                ->setToken('abcde12345')
                ->setExpiresAt((new \DateTimeImmutable())->modify('+ 10 days'))
                ->setTokenType('bearer'),
                1,
                'newAccessToken',
            ],
            [(new Feed())
                ->setToken('abcde12345')
                ->setExpiresAt((new \DateTimeImmutable())->modify('+ 11 days'))
                ->setTokenType('bearer'),
                0,
                'abcde12345',
            ],
        ];
    }
}
