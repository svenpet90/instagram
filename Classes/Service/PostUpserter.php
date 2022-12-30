<?php

declare(strict_types=1);

namespace SvenPetersen\Instagram\Service;

use SvenPetersen\Instagram\Client\ApiClientInterface;
use SvenPetersen\Instagram\Domain\DTO\PostDTO;
use SvenPetersen\Instagram\Domain\Model\Post;
use SvenPetersen\Instagram\Domain\Repository\PostRepository;
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

    public function __construct(
        PostRepository $postRepository,
        PersistenceManagerInterface $persistenceManager
    ) {
        $this->postRepository = $postRepository;
        $this->persistenceManager = $persistenceManager;
    }

    public function upsertPost(PostDTO $dto, int $storagePid, ApiClientInterface $apiClient): Post
    {
        $action = 'UPDATE';
        $post = $this->postRepository->findOneBy(['instagramid' => $dto->getId(), 'pid' => $storagePid]);

        if ($post === null) {
            $action = 'NEW';
        }

        $post = $this->upsertFromDTO($dto, $post);
        $post
            ->setFeed($apiClient->getFeed())
            ->setPid($storagePid);

        $this->postRepository->add($post);
        $this->persistenceManager->persistAll();

        if ($action === 'UPDATE') {
            return $post;
        }

        return $this->processPost($post, $dto);
    }

    private function processPost(Post $post, PostDTO $dto): Post
    {
        switch ($post->getMediaType()) {
            case Post::MEDIA_TYPE_IMAGE:
                $fileObject = $this->downloadFile($dto->getMediaUrl(), Post::IMAGE_FILE_EXT);
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
            case Post::MEDIA_TYPE_VIDEO:
                $fileObject = $this->downloadFile($dto->getMediaUrl(), Post::VIDEO_FILE_EXT);
                $this->addToFal($post, $fileObject, self::TABLENAME, 'videos');

                // Download thumbnail image
                $fileObject = $this->downloadFile($dto->getThumbnailUrl(), Post::IMAGE_FILE_EXT);
                $this->addToFal($post, $fileObject, self::TABLENAME, 'images');

                break;
        }

        $this->postRepository->update($post);
        $this->persistenceManager->persistAll();

        return $post;
    }

    private function downloadFile(string $fileUrl, string $fileExtension): File
    {
        $directory = sprintf('%s/fileadmin/instagram', Environment::getPublicPath());
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
            'table_local' => 'sys_file',
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
            ->setInstagramid($dto->getId())
            ->setLink($dto->getPermalink())
            ->setLastupdate(new \DateTimeImmutable())
            ->setCaption(EmojiRemover::filter($dto->getCaption()));

        if ($dto->getCaption() !== '') {
            $hashtags = $this->extractHashtags($dto->getCaption());
            $hashtagsString = implode(' ', $hashtags);

            $post->setHashtags($hashtagsString);
        }

        return $post;
    }

    /**
     * todo: add test
     *
     * @return string[]
     */
    private function extractHashtags(string $text): array
    {
        preg_match_all('/#(\\w+)/', $text, $hashtags);

        return $hashtags[0];
    }
}
