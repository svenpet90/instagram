<?php

declare(strict_types=1);

namespace SvenPetersen\Instagram\Domain\Repository;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

final class PostRepository extends Repository
{
    protected $defaultOrderings = [
        'createdtime' => QueryInterface::ORDER_DESCENDING,
    ];

    public function initializeObject()
    {
        $querySettings = GeneralUtility::makeInstance(Typo3QuerySettings::class);
        $querySettings->setRespectStoragePage(false);

        $this->setDefaultQuerySettings($querySettings);
    }

    public function findBySettings(array $settings)
    {
        $query = $this->createQuery();

        $constraints = [
         //   $this->getAccountConstraints($settings['accounts'], $query),
            $this->getHashtagConstraints($settings['hashtags'], $query),
            $this->getTypeConstraints($settings['types'], $query),
        ];

        if ($settings['limit']) {
            $query->setLimit($settings['limit']);
        }

        return $query
            ->matching($query->logicalAnd($constraints))
            ->execute()
            ->toArray();
    }

    private function getAccountConstraints(array $accounts, QueryInterface $query)
    {
        $accountConstraints = [];

        foreach ($accounts as $account) {
            $accountConstraints[] = $query->equals('account', $account);
        }

        return $query->logicalOr($accountConstraints);
    }

    private function getHashtagConstraints($config, QueryInterface $query)
    {
        if ($config['tags'] === []) {
            return [];
        }

        $hashtagConstraints = [];

        foreach ($config['tags'] as $hashtag) {
            $hashtagConstraints[] = $query->like('tags', '%' . $hashtag . '%');
        }

        $logicalConstraint = $config['logicalConstraint'];

        return $query->$logicalConstraint($hashtagConstraints);
    }

    /**
     * @param string[] $typesToShow
     * @param QueryInterface $query
     *
     * @return \TYPO3\CMS\Extbase\Persistence\Generic\Qom\OrInterface
     */
    private function getTypeConstraints(array $typesToShow, QueryInterface $query)
    {
        $typeConstraints = [];

        /** @var string $type */
        foreach ($typesToShow as $type) {
            $typeConstraints[] = $query->equals('type', $type);
        }

        return $query->logicalOr($typeConstraints);
    }
}
