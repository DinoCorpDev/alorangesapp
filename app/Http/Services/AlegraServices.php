<?php

namespace App\Http\Services;

use Exception;
use GuzzleHttp\Client;

class AlegraServices
{
    private $client;
    private $exclude = [13, 14, 18, 44, 52, 56, 57, 64, 65, 70, 80, 86, 88, 97, 101, 120, 135, 139, 141, 150, 152, 153, 154, 155, 147, 148, 149, 151];
    private $options;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.alegra.com/api/v1/',
            'verify' => false,
            'timeout' => 60,
            'connect_timeout' => 15,
        ]);

        $this->options = [
            'headers' => [
                'accept' => 'application/json',
                'authorization' => 'Basic YWxvcmFuZ2Vjb3Jwb3JhdGlvbkBnbWFpbC5jb206ZDQzNmJjZWJhM2Q5YTVlNjdkZjc=',
            ],
        ];
    }

    public function getCategories()
    {
        return array_values(array_filter($this->fetchPaginated('item-categories', [], 20), function ($object) {
            return !in_array($object['id'], $this->exclude);
        }));
    }

    public function getAllProducts()
    {
        return $this->getProducts();
    }

    public function getProducts()
    {
        return $this->fetchPaginated('items', [], 30);
    }

    public function getProductsByCategory($idCategory)
    {
        return $this->fetchPaginated('items', ['idItemCategory' => $idCategory], 30);
    }

    public function getProductsByQuery($query)
    {
        return array_values(array_filter($this->fetchPaginated('items', ['query' => $query], 30), function ($object) {
            if (!isset($object['itemCategory']['id'])) {
                return false;
            }

            return !in_array($object['itemCategory']['id'], $this->exclude);
        }));
    }

    public function eachProduct(callable $callback, array $params = [], int $limit = 30): int
    {
        return $this->eachPaginated('items', $params, $callback, $limit);
    }

    private function fetchPaginated(string $endpoint, array $params = [], int $limit = 30): array
    {
        $items = [];

        $this->eachPaginated($endpoint, $params, function ($item) use (&$items) {
            $items[] = $item;
        }, $limit);

        return $items;
    }

    private function eachPaginated(string $endpoint, array $params, callable $callback, int $limit): int
    {
        $start = 0;
        $total = 0;

        while (true) {
            $response = $this->requestPage($endpoint, array_merge($params, [
                'start' => $start,
                'limit' => $limit,
            ]));

            if (empty($response)) {
                break;
            }

            foreach ($response as $item) {
                $callback($item);
                $total++;
            }

            $count = count($response);
            $start += $count;

            if ($count < $limit) {
                break;
            }
        }

        return $total;
    }

    private function requestPage(string $endpoint, array $query): array
    {
        $attempt = 0;
        $options = $this->options;
        $options['query'] = $query;

        retry:
        try {
            $responseService = $this->client->request('GET', $endpoint, $options);
            $response = json_decode($responseService->getBody(), true);

            return is_array($response) ? $response : [];
        } catch (Exception $e) {
            $attempt++;

            if ($attempt < 4) {
                sleep($attempt * 2);
                goto retry;
            }

            throw $e;
        }
    }
}
