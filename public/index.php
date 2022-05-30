<?php

require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use App\App;
use App\Command\AuthorCreateAndUpdateCommand;
use App\Command\ProfileAndVideosCreationAndUpdationCommand;
use App\Command\VideoCreateAndUpdateCommand;
use App\Query\AuthorFinderQuery;
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

$app = new App(
    $configProvider,
    new AuthorFinderQuery($fetcher),
    new ActionRunner((new GithubActionRunStarterAndArtifactDownloaderFactory())->make()),
    new ProfileAndVideosCreationAndUpdationCommand(
        new AuthorCreateAndUpdateCommand($fetcher),
        new VideoCreateAndUpdateCommand($fetcher)
    )
);

/** @var string $requestUrl */
$requestUrl = $_SERVER['REQUEST_URI'];

/** @var string|null $queryParameters */
$queryParameters = ! empty($_SERVER['QUERY_STRING']) ? ('?' . $_SERVER['QUERY_STRING']) : null;

/** @var string $calledEndPoint */
$calledEndPoint = $queryParameters
    ? str_replace($queryParameters, '', $requestUrl)
    : $requestUrl
;

if (strlen($calledEndPoint) > 1 && substr($calledEndPoint, -1) === '/') {
    /** @var string $calledEndPoint */
    $calledEndPoint = substr($calledEndPoint, 0, -1);
}

$app->run($requestUrl, $queryParameters, $_SERVER['HTTP_AUTHORIZATION'] ?? null);

exit;
