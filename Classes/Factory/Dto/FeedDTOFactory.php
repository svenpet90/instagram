<?php

declare(strict_types=1);

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
