<?php

namespace App\Command;

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
            $entities[] = [];
        }
        
        return $entities;
    }
}
