<?php

declare(strict_types=1);

namespace SvenPetersen\Instagram\Command;

use GuzzleHttp\Exception\ClientException;
use SvenPetersen\Instagram\Client\InstagramApiClient;
use SvenPetersen\Instagram\Domain\Model\Account;
use SvenPetersen\Instagram\Domain\Model\Longlivedaccesstoken;
use SvenPetersen\Instagram\Domain\Model\Post;
use SvenPetersen\Instagram\Domain\Repository\AccountRepository;
use SvenPetersen\Instagram\Domain\Repository\LonglivedaccesstokenRepository;
use SvenPetersen\Instagram\Domain\Repository\PostRepository;
use SvenPetersen\Instagram\Factory\AccountFactory;
use SvenPetersen\Instagram\Factory\PostFactory;
use SvenPetersen\Instagram\Service\InstagramService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;

class ImportPostsCommand extends Command
{
    private InstagramApiClient $instagramApiClient;

    private PostRepository $postRepository;

    private AccountRepository $accountRepository;

    private LonglivedaccesstokenRepository $longlivedaccesstokenRepository;

    private PersistenceManagerInterface $persistenceManager;

    private AccountFactory $accountFactory;

    private InstagramService $instagramService;

    private PostFactory $postFactory;

    private ?OutputInterface $output = null;

    public function __construct(
        InstagramApiClient $instagramApiClient,
        PostRepository $postRepository,
        AccountRepository $accountRepository,
        LonglivedaccesstokenRepository $longlivedaccesstokenRepository,
        PersistenceManagerInterface $persistenceManager,
        AccountFactory $accountFactory,
        InstagramService $instagramService,
        PostFactory $postFactory,
        $name = null
    ) {
        parent::__construct($name);

        $this->instagramApiClient = $instagramApiClient;
        $this->postRepository = $postRepository;
        $this->accountRepository = $accountRepository;
        $this->longlivedaccesstokenRepository = $longlivedaccesstokenRepository;
        $this->persistenceManager = $persistenceManager;
        $this->accountFactory = $accountFactory;
        $this->instagramService = $instagramService;
        $this->postFactory = $postFactory;
    }

    protected function configure()
    {
        $this
            ->setHelp('Imports Posts for a given instagram account')
            ->addArgument('userId', InputArgument::REQUIRED, 'Instagram User/Account ID to import posts for')
            ->addArgument('storagePid', InputArgument::REQUIRED, 'The PID where to save the post records');
    }

    /**
     * Creates or updates an account to add posts to
     */
    public function upsertAccount(?Account $account, array $igUserData, int $storagePid, Longlivedaccesstoken $longlivedaccesstoken): Account
    {
        if (null === $account) {
            $account = $this->accountFactory->createFromAPIResponse($igUserData);

            $this->accountRepository->add($account);
            $this->persistenceManager->persistAll();

            $this->output->writeln('Created new Account for username: ' . $igUserData['username'] . "\n");
        }

        $account->setSysLanguageUid(-1);
        $account->setPid($storagePid);
        $account->setLonglivedaccesstoken($longlivedaccesstoken);

        $this->accountRepository->update($account);
        $this->persistenceManager->persistAll();

        return $account;
    }

    /**
     * Adds or updates a Post - depending on the given action - and adds it to a given account.
     *
     * @throws IllegalObjectTypeException
     * @throws UnknownObjectException
     */
    public function importPost(int $postId, Account $account, int $storagePid): Post
    {
        $postData = $this->instagramApiClient->getMedia($postId);
        $post = $this->postFactory->createFromAPIResponse($postData);

        $post->setPid($storagePid);
        $post->setSysLanguageUid(-1);
        $this->postRepository->add($post);

        $account->addPost($post);
        $this->accountRepository->update($account);
        $this->persistenceManager->persistAll();

        switch ($post->getType()) {
            case 'CAROUSEL_ALBUM':
                $childMediaIds = $this->instagramApiClient->getChildrenMediaIds($postData['id']);
                $childMedias = $this->instagramService->getCarouselMedia($childMediaIds);

                foreach ($childMedias as $item) {
                    switch ($item['media_type']) {
                        case 'IMAGE':
                            $fileObject = $this->downloadFile(
                                $item['media_url'],
                                'jpg'
                            );

                            $this->addToFal($post, $fileObject, 'tx_instagram_domain_model_post', 'images');

                            break;
                        case 'VIDEO':
                            $fileObject = $this->downloadFile($item['media_url'], 'mp4');
                            $this->addToFal($post, $fileObject, 'tx_instagram_domain_model_post', 'videos');

                            break;
                    }
                }

                break;
            case 'VIDEO':
                $fileObject = $this->downloadFile($postData['media_url'], 'mp4');
                $this->addToFal($post, $fileObject, 'tx_instagram_domain_model_post', 'videos');

                $fileObject = $this->downloadFile($postData['thumbnail_url'], 'jpg');
                $this->addToFal($post, $fileObject, 'tx_instagram_domain_model_post', 'images');

                break;
            case 'IMAGE':
                $fileObject = $this->downloadFile($postData['media_url'], 'jpg');
                $this->addToFal($post, $fileObject, 'tx_instagram_domain_model_post', 'images');

                break;
        }

        $this->postRepository->update($post);
        $this->persistenceManager->persistAll();

        return $post;
    }

    /**
     * Downloads a file from a given URL with the given fileextension
     * Return an fileObject of the downloaded file.
     *
     * @return File|Folder
     *
     * @throws \TYPO3\CMS\Core\Resource\Exception\ResourceDoesNotExistException
     */
    public function downloadFile(string $fileUrl, string $type)
    {
        $directory = Environment::getPublicPath() . '/fileadmin/instagram';
        GeneralUtility::mkdir_deep($directory);

        $directory = str_replace('1:', 'uploads', $directory);
        $filePath = $directory . '/instagram-' . md5($fileUrl) . '.' . $type;

        $data = file_get_contents($fileUrl);
        file_put_contents($filePath, $data);

        return GeneralUtility::makeInstance(ResourceFactory::class)->retrieveFileOrFolderObject($filePath);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->output = $output;

        $userId = $input->getArgument('userId');
        $storagePid = (int)$input->getArgument('storagePid');

        $longlivedToken = $this->longlivedaccesstokenRepository->findOneByUserid((int)$userId);

        if (!$longlivedToken instanceof Longlivedaccesstoken) {
            throw new \Exception('Kein Longlivedaccesstoken gefunden!');
        }

        $accesstoken = $longlivedToken->getToken();
        $this->instagramApiClient->setAccesstoken($accesstoken);

        try {
            $instagramUser = $this->instagramApiClient->getUserdata($userId);
        } catch (ClientException $exception) {
            $message = $exception->getMessage();

            if (strpos($message, 'Application request limit reached') !== false) {
                $io->warning('The APIs rate limit of 200 requests/hour is exhausted. Please try again later.');

                return Command::FAILURE;
            }
        }

        $account = $this->accountRepository->findOneByUserid($instagramUser['id']);
        $account = $this->upsertAccount($account, $instagramUser, $storagePid, $longlivedToken);

        $alreadyImportedPostsForAccount = $account->getPosts();

        $this->output->writeln([
            'Importing posts for IG-Account: ' . $account->getUsername(),
            '============',
            '',
        ]);

        $posts = $this->instagramApiClient->getPostsRecursive($userId);
        $this->output->writeln('Got ' . count($posts) . ' Posts for the given Account from the API');

        foreach ($posts as $postData) {
            $post = $this->postRepository->findOneByInstagramid($postData['id']);

            if (null === $post) {
                $this->output->writeln('Importing new Post with ID: ' . $postData['id']);
                $this->importPost((int)$postData['id'], $account, $storagePid);
            } else {
                $this->output->writeln('Skipping already imported Post with ID: ' . $postData['id']);
                $alreadyImportedPostsForAccount->detach($post);
            }
        }

        /** @var Post $deletedPost */
        foreach ($alreadyImportedPostsForAccount as $deletedPost) {
            $this->postRepository->remove($deletedPost);
            $this->persistenceManager->persistAll();

            $this->output->writeln('! Post with ID: ' . $deletedPost->getInstagramid() . '("' . $deletedPost->getText() . '") was not in API response and was therefor deleted.');
        }

        return self::SUCCESS;
    }

    /**
     * adds an image to the fal.
     */
    protected function addToFal(Post $newElement, File $file, string $tablename, string $fieldname): void
    {
        $fields = [
            'pid' => $newElement->getPid(),
            'uid_local' => $file->getUid(),
            'uid_foreign' => $newElement->getUid(),
            'tablenames' => $tablename,
            'table_local' => 'sys_file',
            'fieldname' => $fieldname,
            'l10n_diffsource' => '',
            'sorting_foreign' => $file->getUid(),
            'tstamp' => time(),
            'crdate' => time(),
        ];

        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);

        $databaseConn = $connectionPool->getConnectionForTable('sys_file_reference');
        $databaseConn->insert('sys_file_reference', $fields);
    }
}
