<?php

namespace AppTest\Command;

use App\Command\ProfileAndVideosCreationAndUpdationCommand;
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

        foreach ($entities as $entity) {
            self::assertInstanceOf(Video::class, $entity);
        }
    }

}
