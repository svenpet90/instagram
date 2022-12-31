<?php

declare(strict_types=1);

namespace SvenPetersen\Instagram\Domain\Repository;

use SvenPetersen\Instagram\Domain\Model\Feed;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * @method Feed|null findOneByUsername(string $username)
 */
class FeedRepository extends Repository
{
}
