<?php

require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use App\App;
use App\Command\AuthorCreateAndUpdateCommand;
use App\Command\ProfileAndVideosCreationAndUpdationCommand;
use App\Command\VideoCreateAndUpdateCommand;
use App\Service\ActionRunner;
use PierreMiniggio\ConfigProvider\ConfigProvider;
use PierreMiniggio\DatabaseConnection\DatabaseConnection;
use PierreMiniggio\DatabaseFetcher\DatabaseFetcher;
use PierreMiniggio\GithubActionRunStarterAndArtifactDownloader\GithubActionRunStarterAndArtifactDownloaderFactory;

$configProvider = new ConfigProvider(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);
$dbConfig = $configProvider->get()['db'];
$fetcher = new DatabaseFetcher(new DatabaseConnection(
    $dbConfig['host'],
    $dbConfig['database'],
    $dbConfig['username'],
    $dbConfig['password'],
    DatabaseConnection::UTF8_MB4
));

(new App(
    $configProvider,
    new ActionRunner((new GithubActionRunStarterAndArtifactDownloaderFactory())->make()),
    new ProfileAndVideosCreationAndUpdationCommand(
        new AuthorCreateAndUpdateCommand($fetcher),
        new VideoCreateAndUpdateCommand($fetcher)
    )
))->run();

exit;
