<?php

declare(strict_types=1);

namespace App\Components;

use GuzzleHttp\Client;

final class RestServiceClient
{
    private Client $client;

    public function __construct(
    ) {
        $this->client = new Client([]);
    }

    public function get(string $url, array $query, string $accessToken): array
    {
        $response = $this->client->request('GET', $url, [
            'headers' => [
                'Authorization' => $this->authorizationHeader($accessToken),
            ],
            'query' => $query,
        ]);

        return $this->toArray($response->getBody()->getContents());
    }

    public function post(string $url, array $body, string $accessToken): array
    {
        $response = $this->client->request('POST', $url, [
            'headers' => [
                'Authorization' => $this->authorizationHeader($accessToken),
            ],
            'form_params' => $body,
        ]);

        return $this->toArray($response->getBody()->getContents());
    }

    private function authorizationHeader(string $accessToken): string
    {
        return 'Bearer ' . $accessToken;
    }

    private function toArray(string $json): array
    {
        try {
            return (array)json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (\Exception) {
            return [];
        }
    }
}
