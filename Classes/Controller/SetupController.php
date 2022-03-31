<?php

declare(strict_types=1);

namespace SvenPetersen\Instagram\Controller;

use SvenPetersen\Instagram\Client\InstagramApiClient;
use SvenPetersen\Instagram\Creator\LongLivedAccessTokenCreator;
use SvenPetersen\Instagram\Domain\Model\Account;
use SvenPetersen\Instagram\Domain\Model\Longlivedaccesstoken;
use SvenPetersen\Instagram\Domain\Repository\AccountRepository;
use SvenPetersen\Instagram\Factory\AccountFactory;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;

final class SetupController extends ActionController
{
    private LongLivedAccessTokenCreator $longLivedAccessTokenCreator;

    private InstagramApiClient $instagramApiClient;

    private AccountFactory $accountFactory;
    private AccountRepository $accountRepository;
    private PersistenceManagerInterface $persistenceManager;

    public function __construct(
        InstagramApiClient $instagramApiClient,
        LongLivedAccessTokenCreator $longLivedAccessTokenCreator,
        AccountFactory $accountFactory,
        AccountRepository $accountRepository,
        PersistenceManagerInterface $persistenceManager
    ) {
        $this->longLivedAccessTokenCreator = $longLivedAccessTokenCreator;
        $this->instagramApiClient = $instagramApiClient;
        $this->accountFactory = $accountFactory;
        $this->accountRepository = $accountRepository;
        $this->persistenceManager = $persistenceManager;
    }

    public function steponeAction(): void
    {
    }

    public function steptwoAction(): void
    {
        $appId = $this->request->getArgument('clientid');
        $returnUrl = $this->request->getArgument('returnurl');
        $appSecret = $this->request->getArgument('clientsecret');
        $storagePid = $this->request->getArgument('storagePid');

        $link = $this->instagramApiClient->getAuthorizationLink($appId, $returnUrl);

        $this->view->assignMultiple([
            'link' => $link,
            'appId' => $appId,
            'returnUrl' => $returnUrl,
            'appSecret' => $appSecret,
            'storagePid' => $storagePid,
        ]);
    }

    public function stepthreeAction(): void
    {
        $instagramAppId = $this->request->getArgument('clientid');
        $clientSecret = $this->request->getArgument('clientsecret');
        $redirect_uri = $this->request->getArgument('returnurl');
        $code = $this->request->getArgument('code');
        $storagePid = $this->request->getArgument('storagePid');

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

        $accesstoken = $longLivedAccessToken->getToken();
        $this->instagramApiClient->setAccesstoken($accesstoken);
        $igUserData = $this->instagramApiClient->getUserdata($longLivedAccessToken->getUserid());

        $account = $this->accountRepository->findOneByUserid($igUserData['id']);
        $account = $this->upsertAccount($account, $igUserData, (int)$storagePid, $longLivedAccessToken);

        $this->view->assignMultiple([
            'account' => $account,
        ]);
    }

    /**
     * Creates or updates an account to add posts to
     */
    private function upsertAccount(?Account $account, array $igUserData, int $storagePid, Longlivedaccesstoken $longlivedaccesstoken): Account
    {
        if (null === $account) {
            $account = $this->accountFactory->createFromAPIResponse($igUserData);

            $this->accountRepository->add($account);
            $this->persistenceManager->persistAll();
        }

        $account->setSysLanguageUid(-1);
        $account->setPid($storagePid);
        $account->setLonglivedaccesstoken($longlivedaccesstoken);

        $this->accountRepository->update($account);
        $this->persistenceManager->persistAll();

        return $account;
    }
}
