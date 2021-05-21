<?php

namespace App\Command;

use App\Entity\Author;
use App\Entity\Video;

class ProfileAndVideosCreationAndUpdationCommand
{
    /**
     * @param array[] $jsonReponse
     *
     * @return Video[]
     */
    public function createFromJsonResponseAndReturnVideos(array $jsonReponse): array
    {
        $entities = [];

        foreach ($jsonReponse as $jsonReponseEntry) {
            $videoId = $jsonReponseEntry['id'];
            $authorJsonReponseEntry = $jsonReponseEntry['author'];
            $author = new Author(
                $authorJsonReponseEntry['id'],
                $authorJsonReponseEntry['uniqueId']
            );
            $entities[] = new Video(
                $videoId,
                $jsonReponseEntry['desc'],
                $author,
                'https://www.tiktok.com/@' . $author->handle . '/video/' . $videoId
            );
        }

        return $entities;
    }
}
