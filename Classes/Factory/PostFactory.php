<?php

declare(strict_types=1);

namespace SvenPetersen\Instagram\Factory;

use DateTime;
use SvenPetersen\Instagram\Domain\Model\Post;
use SvenPetersen\Instagram\Service\EmojiService;

class PostFactory
{
    public function create(): Post
    {
        return new Post();
    }

    public function createFromAPIResponse(array $apiData): Post
    {
        $post = ($this->create())
            ->setCreatedtime((int)(new DateTime($apiData['timestamp']))->format('U'))
            ->setType($apiData['media_type'])
            ->setInstagramid($apiData['id'])
            ->setLink($apiData['permalink'])
            ->setLastupdate(time())
        ;

        if ($apiData['caption']) {
            $post->setText(EmojiService::remove_emoji($apiData['caption']));

            preg_match_all('/#(\\w+)/', $apiData['caption'], $hashtags);

            if ($hashtags[0]) {
                $hashtagsString = implode(' ', $hashtags[0]);
                $post->setTags($hashtagsString);
            }
        }

        return $post;
    }
}
