<?php

declare(strict_types=1);

namespace SvenPetersen\Instagram\Controller;

use Psr\Http\Message\ResponseInterface;
use SvenPetersen\Instagram\Service\AccessTokenService;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class TokenGeneratorController extends ActionController
{
    public function __construct(
        private readonly ModuleTemplateFactory $moduleTemplateFactory,
        private readonly AccessTokenService $accessTokenService,
    ) {
    }

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

        $storagePid = (int)$this->request->getArgument('storagePid');

        $feed = $this->accessTokenService->createFeed(
            $instagramAppId,
            $clientSecret,
            $redirect_uri,
            $code,
            $storagePid
        );

        $view->assignMultiple([
            'feed' => $feed,
        ]);

        return $view->renderResponse('StepThree');
    }
}
