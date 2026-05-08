<?php

namespace App\FilmsRepository;

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

    public function getFilmByImdbId(string $imdbId): array
    {
        $url = 'https://www.omdbapi.com/?apikey=' . $this->apiKey . '&i=' . $imdbId;

        $request = $this->requestFactory->createRequest(
            'GET',
            $url,
        );

        $response = $this->httpClient->sendRequest($request);

        return json_decode($response->getBody()->getContents(), true);
    }
}
