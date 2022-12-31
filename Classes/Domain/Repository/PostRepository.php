<?php

declare(strict_types=1);

namespace SvenPetersen\Instagram\Domain\Repository;

use SvenPetersen\Instagram\Domain\Model\Post;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * @method Post|null findOneByInstagramid($id)
 */
class PostRepository extends Repository
{
    protected $defaultOrderings = [
        'postedAt' => QueryInterface::ORDER_DESCENDING,
    ];

    /**
     * @param array<string, string> $settings
     *
     * @throws InvalidQueryException
     */
    public function findBySettings(array $settings): QueryResultInterface
    {
        $query = $this->createQuery();
        $constraints = [];

        // MediaType constraints
        if ($settings['mediaTypes'] !== '') {
            $mediaTypeConstraints = [];

            foreach (explode(',', $settings['mediaTypes']) as $mediaType) {
                $mediaTypeConstraints[] = $query->equals('mediaType', $mediaType);
            }

            $constraints[] = $query->logicalOr($mediaTypeConstraints);
        }

        // Hashtag constraints
        if ($settings['hashtagConstraints'] !== '') {
            $hashtagConstraints = [];

            foreach (explode(',', $settings['hashtagConstraints']) as $hashtag) {
                $hashtagConstraints[] = $query->like('hashtags', sprintf('%%%s %%', trim($hashtag)));
            }

            $constraints[] = $query->{$settings['logicalConstraint']}($hashtagConstraints);
        }

        if ($settings['maxPostsToShow']) {
            $query->setLimit((int)$settings['maxPostsToShow']);
        }

        return $query->matching($query->logicalAnd($constraints))->execute();
    }

    /**
     * @param string[] $types
     */
    public function findByTypes(array $types): QueryResultInterface
    {
        $query = $this->createQuery();

        $constrains = [];
        foreach ($types as $type) {
            $constrains[] = $query->equals('mediaType', $type);
        }

        $query->matching($query->logicalOr($constrains));

        return $query->execute();
    }

    /**
     * @param array<string, mixed> $constraints
     * @return Post|null
     */
    public function findOneBy(array $constraints)
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);

        $constrains = [];
        foreach ($constraints as $field => $value) {
            $constrains[] = $query->equals($field, $value);
        }

        $query->matching($query->logicalAnd($constrains));

        /** @var Post|null $result */
        $result = $query
            ->setLimit(1)
            ->execute()
            ->getFirst();

        return $result;
    }

    /**
     * @param string[] $hashtags
     *
     * @throws InvalidQueryException
     */
    public function findByHashtags(array $hashtags, string $logicalConstraint): QueryResultInterface
    {
        $constraints = [];
        $query = $this->createQuery();

        foreach ($hashtags as $tag) {
            $constraints[] = $query->like('tags', '%,' . $tag . ',%');
        }

        $query->matching($query->{$logicalConstraint}($constraints));

        return $query->execute();
    }
}
