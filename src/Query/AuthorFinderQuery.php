<?php

namespace App\Query;

use App\Entity\Author;
use PierreMiniggio\DatabaseFetcher\DatabaseFetcher;

class AuthorFinderQuery
{
    public function __construct(private DatabaseFetcher $fetcher)
    {
    }

    public function findByUsername(string $username): ?Author
    {
        $queriedAuthors = $this->fetcher->query(
            $this->fetcher->createQuery(
                'author'
            )->select(
                'tiktok_id'
            )->where(
                'handle = :handle'
            ),
            ['handle' => $username]
        );

        if (! $queriedAuthors) {
            return null;
        }

        $queriedAuthor = $queriedAuthors[0];

        return new Author($queriedAuthor['tiktok_id'], $username);
    }
}
