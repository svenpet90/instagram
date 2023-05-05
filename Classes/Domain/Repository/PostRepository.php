<?php

declare(strict_types=1);

/*
 * This file is part of the Extension "instagram" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace SvenPetersen\Instagram\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

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

        // Feed constraints
        if ($settings['feeds'] !== '') {
            $feedConstraints = [];

            foreach (explode(',', $settings['feeds']) as $feed) {
                $feedConstraints[] = $query->equals('feed', $feed);
            }

            $constraints[] = $query->logicalOr(...$feedConstraints);
        }

        // MediaType constraints
        if ($settings['mediaTypes'] !== '') {
            $mediaTypeConstraints = [];

            foreach (explode(',', $settings['mediaTypes']) as $mediaType) {
                $mediaTypeConstraints[] = $query->equals('mediaType', $mediaType);
            }

            $constraints[] = $query->logicalOr(...$mediaTypeConstraints);
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

        return $query->matching($query->logicalAnd(...$constraints))->execute();
    }
}
