<?php

declare(strict_types=1);

namespace SvenPetersen\Instagram\Controller;

use SvenPetersen\Instagram\Client\InstagramApiClient;
use SvenPetersen\Instagram\Creator\LongLivedAccessTokenCreator;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

final class SetupController extends ActionController
{
    private LongLivedAccessTokenCreator $longLivedAccessTokenCreator;

    private InstagramApiClient $instagramApiClient;

    public function __construct(
        InstagramApiClient $instagramApiClient,
        LongLivedAccessTokenCreator $longLivedAccessTokenCreator
    ) {
        $this->longLivedAccessTokenCreator = $longLivedAccessTokenCreator;
        $this->instagramApiClient = $instagramApiClient;
    }

    public function steponeAction(): void
    {
    }

    public function steptwoAction(): void
    {
        $appId = $this->request->getArgument('clientid');
        $returnUrl = $this->request->getArgument('returnurl');
        $appSecret = $this->request->getArgument('clientsecret');

        $link = $this->instagramApiClient->getAuthorizationLink($appId, $returnUrl);

        $this->view->assignMultiple([
            'link' => $link,
            'appId' => $appId,
            'returnUrl' => $returnUrl,
            'appSecret' => $appSecret,
        ]);
    }

    public function stepthreeAction(): void
    {
        $instagramAppId = $this->request->getArgument('clientid');
        $clientSecret = $this->request->getArgument('clientsecret');
        $redirect_uri = $this->request->getArgument('returnurl');
        $code = $this->request->getArgument('code');

        $accessTokenData = $this->instagramApiClient->getAccessToken(
            $instagramAppId,
            $clientSecret,
            $redirect_uri,
            $code
        );

        $longLivedAccessToken = $this->longLivedAccessTokenCreator->create(
            $clientSecret,
            $accessTokenData['access_token'],
            (int)$accessTokenData['user_id']
        );

        $this->view->assignMultiple([
            'userId' => $longLivedAccessToken->getUserid(),
            'longLivedAccessToken' => $longLivedAccessToken->getToken(),
        ]);
    }
}
