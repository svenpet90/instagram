<?php

declare(strict_types=1);

/*
 * This file is part of the Extension "instagram" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace SvenPetersen\Instagram\Service;

use Psr\EventDispatcher\EventDispatcherInterface;
use SvenPetersen\Instagram\Client\ApiClientInterface;
use SvenPetersen\Instagram\Domain\Model\Dto\PostDTO;
use SvenPetersen\Instagram\Domain\Model\Post;
use SvenPetersen\Instagram\Domain\Repository\PostRepository;
use SvenPetersen\Instagram\Event\Post\PostPersistPostEvent;
use SvenPetersen\Instagram\Event\Post\PrePersistPostEvent;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;

class PostUpserter
{
    private const TABLENAME = 'tx_instagram_domain_model_post';

    private PostRepository $postRepository;

    private PersistenceManagerInterface $persistenceManager;

    private EventDispatcherInterface $eventDispatcher;

    /**
     * @var array<string, string>
     */
    private array $extConf;

    public function __construct(
        PostRepository $postRepository,
        PersistenceManagerInterface $persistenceManager,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->postRepository = $postRepository;
        $this->persistenceManager = $persistenceManager;
        $this->eventDispatcher = $eventDispatcher;

        /** @var ExtensionConfiguration $extensionConfiguration */
        $extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class);
        $this->extConf = $extensionConfiguration->get('instagram');
    }

    /**
     * @param int<0, max> $storagePid
     */
    public function upsertPost(PostDTO $dto, int $storagePid, ApiClientInterface $apiClient): Post
    {
        $querySettings = $this->postRepository->createQuery()->getQuerySettings();
        $querySettings->setStoragePageIds([$storagePid]);
        $this->postRepository->setDefaultQuerySettings($querySettings);

        $action = 'UPDATE';

        /** @var Post|null $post */
        $post = $this->postRepository->findOneBy([
            'instagram_id' => $dto->getId(),
            'pid' => $storagePid,
        ]);

        if ($post === null) {
            $action = 'NEW';
        }

        $post = $this->upsertFromDTO($dto, $post);
        $post
            ->setFeed($apiClient->getFeed())
            ->setPid($storagePid);

        /** @var PrePersistPostEvent $event */
        $event = $this->eventDispatcher->dispatch(new PrePersistPostEvent($post, $action));
        $post = $event->getPost();

        $this->postRepository->add($post);
        $this->persistenceManager->persistAll();

        if ($action === 'UPDATE') {
            $this->eventDispatcher->dispatch(new PostPersistPostEvent($post));

            return $post;
        }

        return $this->processPostMedia($post, $dto);
    }

    private function processPostMedia(Post $post, PostDTO $dto): Post
    {
        switch ($post->getMediaType()) {
            case Post::MEDIA_TYPE_IMAGE:
                $fileObject = $this->downloadFile($dto->getMediaUrl(), Post::IMAGE_FILE_EXT);
                $this->addToFal($post, $fileObject, self::TABLENAME, 'images');

                break;
            case Post::MEDIA_TYPE_VIDEO:
                $fileObject = $this->downloadFile($dto->getMediaUrl(), Post::VIDEO_FILE_EXT);
                $this->addToFal($post, $fileObject, self::TABLENAME, 'videos');

                // Download thumbnail image
                $fileObject = $this->downloadFile($dto->getThumbnailUrl(), Post::IMAGE_FILE_EXT);
                $this->addToFal($post, $fileObject, self::TABLENAME, 'images');

                break;
            case Post::MEDIA_TYPE_CAROUSEL_ALBUM:
                $children = $dto->getChildren();

                foreach ($children as $child) {
                    switch ($child->getMediaType()) {
                        case Post::MEDIA_TYPE_IMAGE:
                            $fileObject = $this->downloadFile(
                                $child->getMediaUrl(),
                                Post::IMAGE_FILE_EXT
                            );

                            $this->addToFal($post, $fileObject, self::TABLENAME, 'images');

                            break;
                        case Post::MEDIA_TYPE_VIDEO:
                            $fileObject = $this->downloadFile($child->getMediaUrl(), Post::VIDEO_FILE_EXT);
                            $this->addToFal($post, $fileObject, self::TABLENAME, 'videos');

                            break;
                    }
                }

                break;
        }

        $this->postRepository->update($post);
        $this->persistenceManager->persistAll();

        $this->eventDispatcher->dispatch(new PostPersistPostEvent($post));

        return $post;
    }

    private function downloadFile(string $fileUrl, string $fileExtension): File
    {
        $relativeFilePath = $this->extConf['local_file_storage_path'];
        $directory = sprintf('%s%s', Environment::getProjectPath(), $relativeFilePath);
        GeneralUtility::mkdir_deep($directory);

        $directory = str_replace('1:', 'uploads', $directory);
        $filePath = sprintf('%s/%s.%s', $directory, md5($fileUrl), $fileExtension);
        $data = file_get_contents($fileUrl);
        file_put_contents($filePath, $data);

        /** @var ResourceFactory $resourceFactory */
        $resourceFactory = GeneralUtility::makeInstance(ResourceFactory::class);

        /** @var File $file */
        $file = $resourceFactory->retrieveFileOrFolderObject($filePath);

        return $file;
    }

    private function addToFal(Post $newElement, File $file, string $tablename, string $fieldname): void
    {
        $fields = [
            'pid' => $newElement->getPid(),
            'uid_local' => $file->getUid(),
            'uid_foreign' => $newElement->getUid(),
            'tablenames' => $tablename,
            'fieldname' => $fieldname,
            'l10n_diffsource' => '',
            'sorting_foreign' => $file->getUid(),
            'tstamp' => time(),
            'crdate' => time(),
        ];

        /** @var ConnectionPool $connectionPool */
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);

        $databaseConn = $connectionPool->getConnectionForTable('sys_file_reference');
        $databaseConn->insert('sys_file_reference', $fields);
    }

    private function upsertFromDTO(PostDTO $dto, Post $post = null): Post
    {
        if ($post === null) {
            $post = new Post();
        }

        $post
            ->setPostedAt($dto->getTimestamp())
            ->setMediaType($dto->getMediatype())
            ->setInstagramId($dto->getId())
            ->setLink($dto->getPermalink())
            ->setCaption(EmojiRemover::filter($dto->getCaption()));

        if ($dto->getCaption() !== '') {
            $hashtags = $this->extractHashtags($dto->getCaption());
            $hashtagsString = implode(' ', $hashtags);

            $post->setHashtags($hashtagsString);
        }

        return $post;
    }

    /**
     * @return string[]
     */
    private function extractHashtags(string $text): array
    {
        preg_match_all('/#(\\w+)/', $text, $hashtags);

        return $hashtags[0];
    }
}
