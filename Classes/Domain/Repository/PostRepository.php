<?php

declare(strict_types=1);

namespace SvenPetersen\Instagram\Domain\Repository;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

class PostRepository extends Repository
{
    protected $defaultOrderings = [
        'postedAt' => QueryInterface::ORDER_DESCENDING,
    ];

    public function initializeObject()
    {
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

    public function findOneBy(array $constraints)
    {
        $query = $this->createQuery();

        $constrains = [];
        foreach ($constraints as $field => $value) {
            $constrains[] = $query->equals($field, $value);
        }

        $query->matching($query->logicalAnd($constrains));

        return $query
            ->setLimit(1)
            ->execute()
            ->getFirst();
    }

    public function findImagesByHashtags(array $hashtags, string $logicalConstraint): QueryResultInterface
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
