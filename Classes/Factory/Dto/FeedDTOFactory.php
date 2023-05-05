<?php

declare(strict_types=1);

/*
 * This file is part of the Extension "instagram" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace SvenPetersen\Instagram\Factory\Dto;

use SvenPetersen\Instagram\Domain\Model\Dto\FeedDTO;

/**
 * @internal
 */
class FeedDTOFactory
{
    /**
     * @param mixed[] $apiResponseDate
     */
    public static function createFromApiResponse(array $apiResponseDate): FeedDTO
    {
        return new FeedDTO(
            $apiResponseDate['id'],
            $apiResponseDate['username'],
            $apiResponseDate['account_type'],
            $apiResponseDate['media_count']
        );
    }
}
