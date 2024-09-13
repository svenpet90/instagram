<?php

declare(strict_types=1);

namespace SvenPetersen\Instagram\Controller;

use Psr\Http\Message\ResponseInterface;
use SvenPetersen\Instagram\Factory\FeedFactory;
use SvenPetersen\Instagram\Service\AccessTokenService;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class TokenGeneratorController extends ActionController
{
    public function __construct(
        private readonly ModuleTemplateFactory $moduleTemplateFactory,
        private readonly AccessTokenService $accessTokenService,
        private readonly FeedFactory $feedFactory,
    ) {}

    public function stepOneAction(): ResponseInterface
    {
        $view = $this->moduleTemplateFactory->create($this->request);

        return $view->renderResponse('StepOne');
    }

    public function stepTwoAction(): ResponseInterface
    {
        $view = $this->moduleTemplateFactory->create($this->request);

        /** @var string $appId */
        $appId = $this->request->getArgument('clientid');

        /** @var string $returnUrl */
        $returnUrl = $this->request->getArgument('returnurl');

        /** @var string $appSecret */
        $appSecret = $this->request->getArgument('clientsecret');

        $storagePid = (int)$this->request->getArgument('storagePid');

        $link = $this->accessTokenService->getAuthorizationLink($appId, $returnUrl);

        $view->assignMultiple([
            'link' => $link,
            'appId' => $appId,
            'returnUrl' => $returnUrl,
            'appSecret' => $appSecret,
            'storagePid' => $storagePid,
        ]);

        return $view->renderResponse('StepTwo');
    }

    public function stepThreeAction(): ResponseInterface
    {
        $view = $this->moduleTemplateFactory->create($this->request);

        /** @var string $instagramAppId */
        $instagramAppId = $this->request->getArgument('clientid');

        /** @var string $clientSecret */
        $clientSecret = $this->request->getArgument('clientsecret');

        /** @var string $redirect_uri */
        $redirect_uri = $this->request->getArgument('returnurl');

        /** @var string $code */
        $code = $this->request->getArgument('code');

        /** @var int<0, max> $storagePid */
        $storagePid = (int)$this->request->getArgument('storagePid');

        // get access token
        $accessTokenResponse = $this->accessTokenService->getAccessToken($instagramAppId, $clientSecret, $redirect_uri, $code);
        $accessToken = $accessTokenResponse['access_token'];
        $me = $this->accessTokenService->me($accessToken);

        // get long-lived access token
        $longLivedAccessTokenResponse = $this->accessTokenService->getLongLivedAccessToken($clientSecret, $accessToken);
        $expiresAt = (new \DateTimeImmutable())->modify(sprintf('+ %s seconds', $longLivedAccessTokenResponse['expires_in']));

        $feed = $this->feedFactory->upsert(
            $longLivedAccessTokenResponse['access_token'],
            $longLivedAccessTokenResponse['token_type'],
            (string)$accessTokenResponse['user_id'],
            $expiresAt,
            $me['username'],
            $storagePid,
            $me
        );

        $view->assignMultiple([
            'feed' => $feed,
        ]);

        return $view->renderResponse('StepThree');
    }
}
