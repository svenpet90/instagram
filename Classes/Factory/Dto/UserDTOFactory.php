<?php

declare(strict_types=1);

namespace SvenPetersen\Instagram\Factory\Dto;

use SvenPetersen\Instagram\Domain\Model\Dto\UserDTO;

/**
 * @internal
 */
class UserDTOFactory
{
    /**
     * @param mixed[] $apiResponseDate
     */
    public static function createFromApiResponse(array $apiResponseDate): UserDTO
    {
        return new UserDTO(
            $apiResponseDate['id'],
            $apiResponseDate['username'],
            $apiResponseDate['account_type'],
            $apiResponseDate['media_count']
        );
    }
}
