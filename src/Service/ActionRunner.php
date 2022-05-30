<?php

namespace App\Service;

use Exception;
use PierreMiniggio\GithubActionRunStarterAndArtifactDownloader\GithubActionRunStarterAndArtifactDownloader;
use PierreMiniggio\GithubActionRunStarterAndArtifactDownloader\GithubActionRunStarterAndArtifactDownloaderException;

class ActionRunner
{

    public function __construct(private GithubActionRunStarterAndArtifactDownloader $runner)
    {
    }

    public function runPythonProject(
        string $token,
        string $account,
        string $project,
        string $username,
        int $numberOfVideos
    ): string
    {
        try {
            $artifacts = $this->runner->runActionAndGetArtifacts(
                token: $token,
                owner: $account,
                repo: $project,
                workflowIdOrWorkflowFileName: 'get-videos.yml',
                refreshTime: 330,
                inputs: [
                    'username' => $username,
                    'numberOfVideos' => (string) $numberOfVideos
                ]
            );
        } catch (GithubActionRunStarterAndArtifactDownloaderException $e) {
            throw new Exception('Action failed, message : ' . $e->getMessage() . ', trace : ' . json_encode(
                $e->getTrace()
            ));
        }

        $artifactsCount = count($artifacts);

        if ($artifactsCount !== 1) {
            throw new Exception('Bad artifacts count ' . $artifactsCount . (
                $artifactsCount ? ' (' . json_encode($artifacts) . ')' : ''
            ));
        }

        $responseFileName = $artifacts[0];
        $response = file_get_contents($responseFileName);
        unlink($responseFileName);

        return $response;
    }

    public function runNodeProject(
        string $token,
        string $account,
        string $project,
        string $username
    ): string
    {
        try {
            $artifacts = $this->runner->runActionAndGetArtifacts(
                token: $token,
                owner: $account,
                repo: $project,
                workflowIdOrWorkflowFileName: 'get-profile.yml',
                refreshTime: 50,
                inputs: ['username' => $username]
            );
        } catch (GithubActionRunStarterAndArtifactDownloaderException $e) {
            throw new Exception('Action failed, message : ' . $e->getMessage() . ', trace : ' . json_encode(
                $e->getTrace()
            ));
        }

        $artifactsCount = count($artifacts);

        if ($artifactsCount !== 1) {
            throw new Exception('Bad artifacts count ' . $artifactsCount . (
                $artifactsCount ? ' (' . json_encode($artifacts) . ')' : ''
            ));
        }

        $responseFileName = $artifacts[0];
        $response = file_get_contents($responseFileName);
        unlink($responseFileName);

        return $response;
    }
}
