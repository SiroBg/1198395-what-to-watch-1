<?php

require_once __DIR__ . '/init.php';

use App\Repository\RemoteRepository;
use App\Service\MovieService;
use GuzzleHttp\Client;
use Nyholm\Psr7\Factory\Psr17Factory;

$httpClient = new Client(['verify' => false]);

$requestFactory = new Psr17Factory();

$repository = new RemoteRepository(
    $httpClient,
    $requestFactory,
    $_ENV['OMDB_API_KEY'],
);

$service = new MovieService($repository);

$imdbId = $argv[1] ?? null;

if (!$imdbId) {
    exit('IMDB ID required');
}

$result = $service->getMovie($imdbId);

print_r($result);
