<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class ExternalApiService
{
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function fetchProducts(): array
    {
        $response = $this->client->request('GET', 'https://fakestoreapi.com/products');
        return $response->toArray();
    }
}
