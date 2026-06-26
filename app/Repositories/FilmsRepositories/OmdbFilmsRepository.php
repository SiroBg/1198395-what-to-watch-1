<?php

namespace App\Repositories\FilmsRepositories;

use Exception;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;

class OmdbFilmsRepository implements FilmsRepositoryInterface
{
    public function __construct(
        private ClientInterface $httpClient,
        private RequestFactoryInterface $requestFactory,
        private string $apiKey,
    ) {
    }

    #[\Override]
    public function getFilmByImdbId(string $imdbId): array
    {
        $url = 'https://www.omdbapi.com/?apikey=' . $this->apiKey . '&i=' . $imdbId;

        $request = $this->requestFactory->createRequest(
            'GET',
            $url,
        );

        $response = $this->httpClient->sendRequest($request);

        $result = json_decode($response->getBody()->getContents(), true);

        if (isset($result['Error'])) {
            throw new Exception($result['Error']);
        }

        return $result;
    }
}
