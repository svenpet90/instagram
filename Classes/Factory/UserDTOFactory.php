<?php

namespace SvenPetersen\Instagram\Factory;

use SvenPetersen\Instagram\Domain\DTO\UserDTO;

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
