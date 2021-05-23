<?php declare(strict_types=1);

namespace App\Infrastructure;

use GuzzleHttp\Client;

class GuzzleProducts
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client(
            [
                'base_uri' => sprintf('%s:%s', config('pact.host'), config('pact.port')),
                'headers'  => [
                    'Content-Type' => 'application/vnd.api+json',
                    'Accept'       => 'application/vnd.api+json',
                ],
            ]
        );
    }

    public function all(): array
    {
        $response = $this->client->get('/products');

        return json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR)['data'];
    }
}
