<?php

namespace App;

use App\Command\ProfileAndVideosCreationAndUpdationCommand;
use App\Query\AuthorFinderQuery;
use App\Service\ActionRunner;
use PierreMiniggio\ConfigProvider\ConfigProvider;

class App
{

    public function __construct(
        private ConfigProvider $configProvider,
        private AuthorFinderQuery $authorFinderQuery,
        private ActionRunner $runner,
        private ProfileAndVideosCreationAndUpdationCommand $command
    )
    {
    }

    public function run(
        string $path,
        ?string $queryParameters,
        ?string $authHeader
    ): void
    {
        $config = $this->configProvider->get();

        if (! $authHeader || $authHeader !== 'Bearer ' . $config['apiToken']) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            
            return;
        }

        $pullPrefix = '/pull';
        $pullPrefixLength = strlen($pullPrefix);
        if (substr($path, 0, $pullPrefixLength) === $pullPrefix) {
            goto pull;
        }

        http_response_code(404);

        return;

        pull:
        $secondSlash = substr($path, $pullPrefixLength);

        if (substr($secondSlash, 0, 1) !== '/') {
            http_response_code(404);

            return;
        }

        $username = substr($secondSlash, 1);

        $author = $this->authorFinderQuery->findByUsername($username);

        set_time_limit(780);

        if ($author) {
            $nodeProject = $config['nodeProject'];

            $response = $this->runner->runNodeProject(
                $nodeProject['token'],
                $nodeProject['account'],
                $nodeProject['project'],
                $username
            );

            if ($response) {
                $jsonResponse = json_decode($response, true);

                if ($jsonResponse) {
                    $videos = $jsonResponse['videos'] ?? null;

                    if ($videos) {
                        $videos = $this->command->createFromNodeJsonResponseAndReturnVideos($videos, $author);

                        http_response_code(200);
                        echo json_encode($videos);

                        return;
                    }
                }
            }
        }

        $pythonProjects = $config['crawlerProjects'];
        $pythonProject = $pythonProjects[array_rand($pythonProjects)];
        
        $response = $this->runner->runPythonProject(
            $pythonProject['token'],
            $pythonProject['account'],
            $pythonProject['project'],
            $username,
            30
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

        $videos = $this->command->createFromPythonJsonResponseAndReturnVideos($jsonResponse);

        http_response_code(200);
        echo json_encode($videos);
    }
}
