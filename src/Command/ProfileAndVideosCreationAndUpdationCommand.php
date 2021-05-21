<?php

namespace App\Command;

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
            $entities[] = new Video();
        }

        return $entities;
    }
}
