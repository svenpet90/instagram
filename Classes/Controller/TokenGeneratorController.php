<?php

declare(strict_types=1);

namespace SvenPetersen\Instagram\Controller;

use SvenPetersen\Instagram\Service\AccessTokenService;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class TokenGeneratorController extends ActionController
{
    private AccessTokenService $accessTokenService;

    public function __construct(AccessTokenService $accessTokenService)
    {
        $this->accessTokenService = $accessTokenService;
    }

    public function stepOneAction(): void
    {
    }

    public function stepTwoAction(): void
    {
        /** @var string $appId */
        $appId = $this->request->getArgument('clientid');

        /** @var string $returnUrl */
        $returnUrl = $this->request->getArgument('returnurl');

        /** @var string $appSecret */
        $appSecret = $this->request->getArgument('clientsecret');

        $storagePid = (int)$this->request->getArgument('storagePid');

        $link = $this->accessTokenService->getAuthorizationLink($appId, $returnUrl);

        $this->view->assignMultiple([
            'link' => $link,
            'appId' => $appId,
            'returnUrl' => $returnUrl,
            'appSecret' => $appSecret,
            'storagePid' => $storagePid,
        ]);
    }

    public function stepThreeAction(): void
    {
        /** @var string $instagramAppId */
        $instagramAppId = $this->request->getArgument('clientid');

        /** @var string $clientSecret */
        $clientSecret = $this->request->getArgument('clientsecret');

        /** @var string $redirect_uri */
        $redirect_uri = $this->request->getArgument('returnurl');

        /** @var string $code */
        $code = $this->request->getArgument('code');

        $storagePid = (int)$this->request->getArgument('storagePid');

        $feed = $this->accessTokenService->createFeed(
            $instagramAppId,
            $clientSecret,
            $redirect_uri,
            $code,
            $storagePid
        );

        $this->view->assignMultiple([
            'feed' => $feed,
        ]);
    }
}
