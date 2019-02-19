<?php

namespace Shakurov\Coinbase;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class Coinbase
{
    /**
     * @const string
     */
    const BASE_URI = 'https://api.commerce.coinbase.com';

    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
    private $apiVersion;

    public function __construct()
    {
        $this->apiKey = config('coinbase.apiKey');
        $this->apiVersion = config('coinbase.apiVersion');

        $this->client = new Client([
            'base_uri' => self::BASE_URI,
            'headers' => [
                'Content-Type' => 'application/json',
                'X-CC-Api-Key' => $this->apiKey,
                'X-CC-Version' => $this->apiVersion,
            ],
        ]);
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @param string $apiKey
     * @return Coinbase
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * @return string
     */
    public function getApiVersion()
    {
        return $this->apiVersion;
    }

    /**
     * @param string $apiVersion
     * @return Coinbase
     */
    public function setApiVersion($apiVersion)
    {
        $this->apiVersion = $apiVersion;

        return $this;
    }

    /**
     * Make request.
     *
     * @param string $method
     * @param string $uri
     * @param null|array $query
     * @param null|array $params
     * @return array
     */
    public function makeRequest(string $method, string $uri, array $query = [], array $params = [])
    {
        try {
            $response = $this->client->request($method, $uri, ['query' => $query, 'body' => json_encode($params)]);

            return json_decode((string) $response->getBody(), true);
        } catch(GuzzleException $e) {
            Log::error($e->getMessage());
        }
    }

    /**
     * Lists all charges.
     *
     * @param null|array $query
     * @return array
     */
    public function getCharges(array $query = [])
    {
        return $this->makeRequest('get', 'charges', $query);
    }

    /**
     * Creates a new charge.
     *
     * @param  array  $params
     * @return array
     */
    public function createCharge(array $params = [])
    {
        return $this->makeRequest('post', 'charges', $params);
    }

    /**
     * Retrieves an existing charge.
     *
     * @param  string  $chargeId
     * @return array
     */
    public function getCharge($chargeId)
    {
        return $this->makeRequest('get', "charges/{$chargeId}");
    }

    /**
     * Lists all checkouts.
     *
     * @param null|array $query
     * @return array
     */
    public function getCheckouts(array $query = [])
    {
        return $this->makeRequest('get', 'checkouts', $query);
    }

    /**
     * Creates a new checkout.
     *
     * @param  array  $params
     * @return array
     */
    public function createCheckout(array $params = [])
    {
        return $this->makeRequest('post', 'checkouts', $params);
    }

    /**
     * Retrieves an existing checkout.
     *
     * @param  string  $checkoutId
     * @return array
     */
    public function getCheckout($checkoutId)
    {
        return $this->makeRequest('get', "checkouts/{$checkoutId}");
    }

    /**
     * Updates an existing checkout.
     *
     * @param  string  $checkoutId
     * @param  array   $params
     * @return array
     */
    public function updateCheckout($checkoutId, array $params = [])
    {
        return $this->makeRequest('put', "checkouts/{$checkoutId}", $params);
    }

    /**
     * Deletes an existing checkout.
     *
     * @param  string  $checkoutId
     * @return array
     */
    public function deleteCheckout($checkoutId)
    {
        return $this->makeRequest('delete', "checkouts/{$checkoutId}");
    }

    /**
     * Lists all events.
     *
     * @param null|array $query
     * @return array
     */
    public function getEvents(array $query = [])
    {
        return $this->makeRequest('get', 'events', $query);
    }

    /**
     * Retrieves an existing event.
     *
     * @param  string  $eventId
     * @return array
     */
    public function getEvent($eventId)
    {
        return $this->makeRequest('get', "events/{$eventId}");
    }
}
