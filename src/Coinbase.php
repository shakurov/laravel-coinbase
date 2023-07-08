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
    private const BASE_URI = 'https://api.commerce.coinbase.com';

    /**
     * @var Client
     */
    private $client;

    public function __construct()
    {
        $apiKey = config('coinbase.apiKey');
        $apiVersion = config('coinbase.apiVersion');

        $this->client = new Client([
            'base_uri' => Coinbase::BASE_URI,
            'headers' => [
                'Content-Type' => 'application/json',
                'X-CC-Api-Key' => $apiKey,
                'X-CC-Version' => $apiVersion,
            ],
        ]);
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
    private function makeRequest(string $method, string $uri, array $query = [], array $params = [])
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
     * Retrieves an existing charge by supplying its id or 8 character short-code.
     *
     * @param  string  $chargeId  Id or short-code for a previously created charge
     * @return array
     */
    public function getCharge($chargeId)
    {
        return $this->makeRequest('get', "charges/{$chargeId}");
    }

    /**
     * Cancels an existing charge by supplying its id or 8 character short-code.
     *
     * <b>Note:</b> Only new charges can be successfully canceled.
     *
     * @param  string  $chargeId  Id or short-code for a previously created charge
     * @return array
     */
    public function cancelCharge($chargeId)
    {
        return $this->makeRequest('post', "charges/{$chargeId}/cancel");
    }

    /**
     * Resolves an existing, unresolved charge by supplying its id or 8 character short-code.
     *
     * <b>Note:</b> Only unresolved charges can be successfully resolved.
     *
     * @param  string  $chargeId  Id or short-code for a previously created charge
     * @return array
     */
    public function resolveCharge($chargeId)
    {
        return $this->makeRequest('post', "charges/{$chargeId}/resolve");
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
        return $this->makeRequest('post', 'checkouts', [], $params);
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
     * Lists all invoices.
     *
     * @param null|array $query
     * @return array
     */
    public function getInvoices(array $query = [])
    {
        return $this->makeRequest('get', 'invoices', $query);
    }

    /**
     * Creates a new invoice.
     *
     * @param  array  $params
     * @return array
     */
    public function createInvoice(array $params = [])
    {
        return $this->makeRequest('post', 'invoices', $params);
    }

    /**
     * Retrieves an existing invoice by supplying its id or 8 character short-code.
     *
     * @param  string  $invoiceId Id or short-code for a previously created invoice
     * @return array
     */
    public function getInvoice($invoiceId)
    {
        return $this->makeRequest('get', "invoices/{$invoiceId}");
    }

    /**
     * Voids an existing invoice by supplying its id or 8 character short-code.
     *
     * <b>Note:</b> Only invoices with OPEN or VIEWED status can be voided.
     *
     * @param  string  $invoiceId Id or short-code for a previously created invoice
     * @return array
     */
    public function voidInvoice($invoiceId)
    {
        return $this->makeRequest('post', "invoices/{$invoiceId}/void}");
    }

    /**
     * Resolves an existing, unresolved invoice by supplying its id or 8 character short-code.
     *
     * <b>Note:</b> Only invoices with an unresolved charge can be successfully resolved.
     *
     * @param  string  $invoiceId Id or short-code for a previously created invoice
     * @return array
     */
    public function resolveInvoice($invoiceId)
    {
        return $this->makeRequest('post', "invoices/{$invoiceId}/resolve}");
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
