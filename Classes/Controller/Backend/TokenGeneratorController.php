<?php

declare(strict_types=1);

namespace SvenPetersen\Instagram\Controller\Backend;

use Psr\Http\Message\ResponseInterface;
use SvenPetersen\Instagram\Service\AccessTokenService;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class TokenGeneratorController extends ActionController
{
    private AccessTokenService $accessTokenService;

    public function __construct(AccessTokenService $accessTokenService)
    {
        $this->accessTokenService = $accessTokenService;
    }

    public function stepOneAction(): ResponseInterface
    {
        return $this->htmlResponse();
    }

    public function stepTwoAction(): ResponseInterface
    {
        $appId = $this->request->getArgument('clientid');
        $returnUrl = $this->request->getArgument('returnurl');
        $appSecret = $this->request->getArgument('clientsecret');

        $link = $this->accessTokenService->getAuthorizationLink($appId, $returnUrl);

        $this->view->assignMultiple([
            'link' => $link,
            'appId' => $appId,
            'returnUrl' => $returnUrl,
            'appSecret' => $appSecret,
        ]);

        return $this->htmlResponse();
    }

    public function stepThreeAction(): ResponseInterface
    {
        $instagramAppId = $this->request->getArgument('clientid');
        $clientSecret = $this->request->getArgument('clientsecret');
        $redirect_uri = $this->request->getArgument('returnurl');
        $code = $this->request->getArgument('code');

        $feed = $this->accessTokenService->getLongLivedAccessToken(
            $instagramAppId,
            $clientSecret,
            $redirect_uri,
            $code
        );

        $this->view->assignMultiple([
            'feed' => $feed,
        ]);

        return $this->htmlResponse();
    }
}
