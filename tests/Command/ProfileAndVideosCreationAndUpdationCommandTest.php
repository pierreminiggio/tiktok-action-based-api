<?php

namespace AppTest\Command;

use App\Command\ProfileAndVideosCreationAndUpdationCommand;
use App\Entity\Author;
use App\Entity\Video;
use PHPUnit\Framework\TestCase;

class ProfileAndVideosCreationAndUpdationCommandTest extends TestCase
{

    public function testGood(): void
    {
        $mockedResponse = file_get_contents(
            __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'pierreVideos.json'
        );
        $mockedJsonResponse = json_decode($mockedResponse, true);

        $command = new ProfileAndVideosCreationAndUpdationCommand();

        $entities = $command->createFromJsonResponseAndReturnVideos($mockedJsonResponse);

        self::assertSame(count($mockedJsonResponse), count($entities));

        foreach ($entities as $entityIndex => $entity) {
            $this->assertIsGoodVideo($mockedJsonResponse[$entityIndex], $entity);
        }
    }

    protected function assertIsGoodVideo(array $jsonSource, mixed $entity): void
    {
        self::assertInstanceOf(Video::class, $entity);
        self::assertSame($jsonSource['id'], $entity->id);
        self::assertSame($jsonSource['desc'], $entity->caption);
        $this->assertIsGoodAuthor($jsonSource['author'], $entity->author);
        self::assertSame(
            'https://www.tiktok.com/@' . $entity->author->handle . '/video/' . $entity->id,
            $entity->url
        );
    }

    protected function assertIsGoodAuthor(array $jsonSource, mixed $entity): void
    {
        self::assertInstanceOf(Author::class, $entity);
        self::assertSame($jsonSource['id'], $entity->id);
        self::assertSame($jsonSource['uniqueId'], $entity->handle);
    }
}
