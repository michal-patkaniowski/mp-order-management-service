<?php

declare(strict_types=1);

namespace App\Service;

use App\Service\ExternalApiServiceInterface;
use Exception;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ExternalApiService implements ExternalApiServiceInterface
{
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function fetchData(string $url): array
    {
        $response = $this->client->request('GET', $url);
        if ($response->getStatusCode() !== 200) {
            throw new Exception('Failed to fetch data');
        }

        return $response->toArray();
    }
}
