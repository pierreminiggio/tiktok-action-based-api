<?php

require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use App\App;
use App\Command\ProfileAndVideosCreationAndUpdationCommand;
use App\Service\ActionRunner;
use PierreMiniggio\ConfigProvider\ConfigProvider;
use PierreMiniggio\GithubActionRunStarterAndArtifactDownloader\GithubActionRunStarterAndArtifactDownloaderFactory;

(new App(
    new ConfigProvider(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR),
    new ActionRunner((new GithubActionRunStarterAndArtifactDownloaderFactory())->make()),
    new ProfileAndVideosCreationAndUpdationCommand()
))->run();

exit;
