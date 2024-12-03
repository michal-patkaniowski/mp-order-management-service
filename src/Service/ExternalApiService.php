<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Exception;
use App\Service\ExternalApiServiceInterface;

class ExternalApiService implements ExternalApiServiceInterface
{
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function fetchData($url): array
    {
        $response = $this->client->request('GET', $url);
        if ($response->getStatusCode() !== 200) {
            throw new Exception('Failed to fetch data');
        }

        return $response->toArray();
    }
}
