<?php

namespace App;

use App\Command\ProfileAndVideosCreationAndUpdationCommand;
use App\Service\ActionRunner;
use PierreMiniggio\ConfigProvider\ConfigProvider;

class App
{

    public function __construct(
        private ConfigProvider $configProvider,
        private ActionRunner $runner,
        private ProfileAndVideosCreationAndUpdationCommand $command
    )
    {
    }

    public function run(): void
    {
        $config = $this->configProvider->get();
        $projects = $config['crawlerProjects'];
        $project = $projects[array_rand($projects)];

        $response = $this->runner->run(
            $project['token'],
            $project['account'],
            $project['project'],
            'pierreminiggio',
            20
        );

        $jsonResponse = json_decode($response, true);

        if ($jsonResponse === null) {
            http_response_code(500);

            return;
        }

        if (isset($jsonResponse['message']) && $jsonResponse['message'] === 'User not found') {
            http_response_code(404);
            echo json_encode(['message' => 'This user doesn\'t exist on TikTok']);

            return;
        }

        $videos = $this->command->createFromJsonResponseAndReturnVideos($jsonResponse);

        http_response_code(200);
        echo json_encode($videos);
    }
}
