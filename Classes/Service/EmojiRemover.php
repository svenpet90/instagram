<?php

declare(strict_types=1);

/*
 * This file is part of the Extension "instagram" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace SvenPetersen\Instagram\Service;

class EmojiRemover
{
    public static function filter(string $string = ''): string
    {
        $emojiRegex = '/[\x{1F600}-\x{1F64F}]|' .
            '[\x{1F300}-\x{1F5FF}]|' .
            '[\x{1F680}-\x{1F6FF}]|' .
            '[\x{1F700}-\x{1F77F}]|' .
            '[\x{1F780}-\x{1F7FF}]|' .
            '[\x{1F800}-\x{1F8FF}]|' .
            '[\x{2600}-\x{26FF}][\x{FE0F}]?|' .
            '[\x{2700}-\x{27BF}][\x{FE0F}]?|' .
            '[\x{E000}-\x{F8FF}]|' .
            '[\x{FE00}-\x{FE0F}]|' .
            '[\x{1F900}-\x{1F9FF}]|' .
            '[\x{1FA70}-\x{1FAFF}]|' .
            '[\x{1FB00}-\x{1FBFF}]|' .
            '[\x{200D}]|' .
            '[\x{1F1E6}-\x{1F1FF}]{2}/u';

        $clear_string = preg_replace($emojiRegex, '', $string) ?? '';

        return str_replace(' # ', ' ', $clear_string);
    }
}
