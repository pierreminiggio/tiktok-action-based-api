<?php

namespace AppTest\Command;

use App\Command\AuthorCreateAndUpdateCommand;
use App\Command\ProfileAndVideosCreationAndUpdationCommand;
use App\Command\VideoCreateAndUpdateCommand;
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
        $expectedVideoCount = count($mockedJsonResponse);

        $authorCommandMock = $this->createMock(AuthorCreateAndUpdateCommand::class);
        $authorCommandMock->expects(self::once())->method('execute')->willReturn(1);

        $videoCommandMock = $this->createMock(VideoCreateAndUpdateCommand::class);
        $videoCommandMock->expects(self::exactly($expectedVideoCount))->method('execute')->willReturn(
            ...range(1, $expectedVideoCount)
        );

        $command = new ProfileAndVideosCreationAndUpdationCommand(
            $authorCommandMock,
            $videoCommandMock
        );

        $entities = $command->createFromJsonResponseAndReturnVideos($mockedJsonResponse);

        self::assertSame($expectedVideoCount, count($entities));

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
