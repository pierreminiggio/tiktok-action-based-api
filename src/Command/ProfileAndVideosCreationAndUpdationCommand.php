<?php

namespace App\Command;

use App\Entity\Author;
use App\Entity\Video;

class ProfileAndVideosCreationAndUpdationCommand
{

    public function __construct(
        private AuthorCreateAndUpdateCommand $authorCommand,
        private VideoCreateAndUpdateCommand $videoCommand
    )
    {
    }

    /**
     * @param array[] $jsonReponse
     *
     * @return Video[]
     */
    public function createFromJsonResponseAndReturnVideos(array $jsonReponse): array
    {

        if (count($jsonReponse) === 0) {
            return [];
        }

        /** @var Video[] $entities */
        $entities = [];

        /** @var array<string, int> $authorIds */
        $authorIds = [];

        foreach ($jsonReponse as $jsonReponseEntryIndex => $jsonReponseEntry) {
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

            if ($jsonReponseEntryIndex === 0) {
                $authorIds[$author->id] = $this->authorCommand->execute($author);
            }
        }

        $reversedOrderEntities = array_reverse($entities);

        foreach ($reversedOrderEntities as $entity) {
            $this->videoCommand->execute($entity, $authorIds[$entity->author->id]);
        }

        return $entities;
    }
}
