<?php

namespace App\Command;

use App\Entity\Video;
use PierreMiniggio\DatabaseFetcher\DatabaseFetcher;

class VideoCreateAndUpdateCommand
{

    public function __construct(private DatabaseFetcher $fetcher)
    {
    }
    
    public function execute(Video $entity, int $authorId): int
    {
        $table = 'video';

        $selectQueryArgs = [
            $this->fetcher->createQuery(
                $table
            )->select(
                'id'
            )->where(
                'tiktok_id = :tiktok_id'
            ),
            ['tiktok_id' => $entity->id]
        ];

        $queriedEntities = $this->fetcher->query(...$selectQueryArgs);

        if (empty($queriedEntities)) {
            $this->fetcher->exec(
                $this->fetcher->createQuery(
                    $table
                )->insertInto(
                    'tiktok_id, author_id, caption, url',
                    ':tiktok_id, :author_id, :caption, :url'
                ),
                [
                    'tiktok_id' => $entity->id,
                    'author_id' => $authorId,
                    'caption' => $entity->caption,
                    'url' => $entity->url
                ]
            );
            $queriedEntities = $this->fetcher->query(...$selectQueryArgs);
        }

        $id = $queriedEntities[0]['id'];

        $this->fetcher->exec(
            $this->fetcher->createQuery(
                $table
            )->update(
                'tiktok_id = :tiktok_id, author_id = :author_id, caption = :caption, url = :url'
            )->where(
                'id = :id'
            ),
            [
                'id' => $id,
                'tiktok_id' => $entity->id,
                'author_id' => $authorId,
                'caption' => $entity->caption,
                'url' => $entity->url
            ]
        );

        return $id;
    }
}