<?php

declare(strict_types=1);

/*
 * This file is part of the Extension "instagram" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace SvenPetersen\Instagram\Domain\Model\Dto;

class FeedDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $user_id,
        public readonly string $username,
        public readonly string $name,
        public readonly string $account_type,
        public readonly string $profile_picture_url,
        public readonly int $followers_count,
        public readonly int $follows_count,
        public readonly int $media_count,
    ) {}
}
