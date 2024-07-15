<?php

declare(strict_types=1);

/*
 * This file is part of the Extension "instagram" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace SvenPetersen\Instagram\Factory\Dto;

use SvenPetersen\Instagram\Domain\Model\Dto\PostDTO;

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
        $postDTO = self::createFromArray($data);

        if (array_key_exists('children', $data)) {
            $children = [];

            foreach ($data['children']['data'] as $child) {
                $children[] = self::createFromArray($child);
            }

            $postDTO->setChildren($children);
        }

        return $postDTO;
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
            $data['media_url'] ?? '',
            $data['permalink'],
            new \DateTimeImmutable($data['timestamp']),
            $data['username'],
            $data['thumbnail_url'] ?? '',
            $data['media_type']
        );
    }
}
