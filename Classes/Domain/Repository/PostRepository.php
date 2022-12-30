<?php

declare(strict_types=1);

namespace SvenPetersen\Instagram\Domain\Repository;

use SvenPetersen\Instagram\Domain\Model\Post;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
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

    public function initializeObject(): void
    {
        /** @var Typo3QuerySettings $querySettings */
        $querySettings = GeneralUtility::makeInstance(Typo3QuerySettings::class);
        $querySettings->setRespectStoragePage(false);

        $this->setDefaultQuerySettings($querySettings);
    }

    /**
     * @param string[] $types
     */
    public function findByTypes(array $types): QueryResultInterface
    {
        $query = $this->createQuery();

        $constrains = [];
        foreach ($types as $type) {
            $constrains[] = $query->equals('type', $type);
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

        $constrains = [];
        foreach ($constraints as $field => $value) {
            $constrains[] = $query->equals($field, $value);
        }

        $query->matching($query->logicalAnd($constrains));

        /** @var Post|null $result */
        $result =  $query
            ->setLimit(1)
            ->execute()
            ->getFirst();

        return $result;
    }

    /**
     * @param string[] $hashtags
     *
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
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
