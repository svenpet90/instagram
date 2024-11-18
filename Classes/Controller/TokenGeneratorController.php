<?php

declare(strict_types=1);

namespace SvenPetersen\Instagram\Controller;

use Psr\Http\Message\ResponseInterface;
use SvenPetersen\Instagram\Service\AccessTokenService;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class TokenGeneratorController extends ActionController
{
    public function __construct(
        private readonly AccessTokenService $accessTokenService,
    ) {}

    public function stepOneAction(): ResponseInterface
    {
        return $this->htmlResponse();
    }

    public function stepTwoAction(): ResponseInterface
    {
        $appId = $this->request->getArgument('clientid');
        assert(is_string($appId));

        $returnUrl = $this->request->getArgument('returnurl');
        assert(is_string($returnUrl));

        $appSecret = $this->request->getArgument('clientsecret');
        assert(is_string($appSecret));

        $storagePid = (int)$this->request->getArgument('storagePid');
        assert(is_int($storagePid));

        $link = $this->accessTokenService->getAuthorizationLink($appId, $returnUrl);

        $this->view->assignMultiple([
            'link' => $link,
            'appId' => $appId,
            'returnUrl' => $returnUrl,
            'appSecret' => $appSecret,
            'storagePid' => $storagePid,
        ]);

        return $this->htmlResponse();
    }

    public function stepThreeAction(): ResponseInterface
    {
        $instagramAppId = $this->request->getArgument('clientid');
        assert(is_string($instagramAppId));

        $clientSecret = $this->request->getArgument('clientsecret');
        assert(is_string($clientSecret));

        $redirect_uri = $this->request->getArgument('returnurl');
        assert(is_string($redirect_uri));

        $code = $this->request->getArgument('code');
        assert(is_string($code));

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

        return $this->htmlResponse();
    }
}
