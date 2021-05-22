<?php

namespace App\Command;

use App\Entity\Author;
use PierreMiniggio\DatabaseFetcher\DatabaseFetcher;

class AuthorCreateAndUpdateCommand
{

    public function __construct(private DatabaseFetcher $fetcher)
    {
    }
    
    public function execute(Author $entity): int
    {
        
        $table = 'author';

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
                    'tiktok_id, handle',
                    ':tikok_id, :handle'
                ),
                [
                    'tiktok_id' => $entity->id,
                    'handle' => $entity->handle
                ]
            );
            $queriedEntities = $this->fetcher->query(...$selectQueryArgs);
        }

        $id = $queriedEntities[0]['id'];

        $this->fetcher->exec(
            $this->fetcher->createQuery(
                $table
            )->update(
                'tiktok_id = :tiktok_id, handle = :handle'
            )->where(
                'id = :id'
            ),
            [
                'id' => $id,
                'tiktok_id' => $entity->id,
                'handle' => $entity->handle
            ]
        );

        return $id;
    }
}
