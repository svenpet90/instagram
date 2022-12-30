<?php

declare(strict_types=1);

namespace SvenPetersen\Instagram\Factory;

use SvenPetersen\Instagram\Domain\DTO\PostDTO;

/**
 * @internal
 */
class PostDTOFactory
{
    /**
     * @param array<string, mixed> $data
     *
     * @throws \Exception
     */
    public static function create(array $data): PostDTO
    {
        $postDTP = self::createFromArray($data);

        if (array_key_exists('children', $data)) {
            $children = [];

            foreach ($data['children'] as $data) {
                $children[] = self::createFromArray($data);
            }

            $postDTP->setChildren($children);
        }

        return $postDTP;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @throws \Exception
     */
    private static function createFromArray(array $data): PostDTO
    {
        return new PostDTO(
            $data['id'],
            $data['caption'] ?? '',
            $data['media_url'],
            $data['permalink'],
            new \DateTimeImmutable($data['timestamp']),
            $data['username'],
            $data['thumbnail_url'] ?? '',
            $data['media_type']
        );
    }
}
