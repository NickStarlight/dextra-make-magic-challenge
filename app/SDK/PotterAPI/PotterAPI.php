<?php

namespace App\SDK\PotterAPI;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * The Potter API SDK
 *
 * This implements the rules provided by the MAKE MAGIC test from Dextra.
 * 
 * Since there's no public documentation available for the Potter API, this
 * is very raw as we don't know the status codes or possible responses.
 * 
 * @see https://github.com/dextra/challenges/blob/master/backend/MAKE-MAGIC-PT.md
 * @author Nickolas Gomes Moraes
 * @version 1.0
 * @license WTFPL
 */
class PotterAPI
{
    /**
     * The Potter API Endpoint.
     * 
     * @var string
     */
    protected string $apiURL = '';

    /**
     * The Potter API Authentication Key.
     * 
     * @var string
     */
    protected string $secret = '';

    /**
     * How many times failed HTTP
     * requests should be retried.
     * 
     * @var int
     */
    protected int $retryAmount = 0;

    /**
     * For how long sucessfull HTTP
     * requests should be stored in cache.
     * 
     * @var int
     */
    protected int $cacheLifespan = 0;

    /**
     * The API HTTP client instance.
     * 
     * @var Illuminate\Http\Client\PendingRequest
     */
    protected PendingRequest $client;

    /**
     * The status code of the last request.
     * 
     * @var int
     */
    public int $status = -1;

    /**
     * The response from the API.
     * 
     * @var array
     */
    public array $response = [];

    /**
     * Initialize the class.
     * 
     * @param bool $fromCache Wether the response should be retrieved from cache, if available.
     * @param int $timeout How long should the client wait for the response before timing out.
     * @param int $retryThrottle How long should the client wait before retrying a failed request.
     */
    public function __construct(
        public bool $fromCache = false,
        public int $timeout = 5,
        public int $retryThrottle = 100
    ) {
        /**
         * Injects the configurations available on app/config/potter-api.php
         */
        $this->apiURL = config('potter-api.POTTER_API_URL');
        $this->secret = config('potter-api.POTTER_API_SECRET');
        $this->retryAmount = config('potter-api.POTTER_API_RETRY_COUNT');
        $this->cacheLifespan = config('potter-api.POTTER_API_CACHE_LIFESPAN');

        $this->buildHttpClient();
    }

    /**
     * Get the HTTP request response from Potter API or from cache
     * if enabled/exists.
     * 
     * @param string $endpoint The fully qualified endpoint URL
     * 
     * @return void
     */
    public function get(string $endpoint): void
    {
        if ($this->cacheLifespan > 0 && $this->fromCache === true) {
            /** 
             * If there's a hit on the cache, we return the cache as a new
             * HTTP response instance.
             */
            $cache = $this->getFromCache(method: 'GET', endpoint: $endpoint);
            if ($cache !== null) {
                $this->status = 200;
                $this->response = $cache;
                return;
            }

            /** Fetch the API response */
            $response = $this->client->get($endpoint);
            $this->status = $response->status();
            $this->response = $response->json();

            /** If the response is successful we store it on the cache */
            if ($response->successful()) {
                $this->setResponseCache(method: 'GET', endpoint: $endpoint, response: $response);
            }
        } else {
            $response = $this->client->get($endpoint);
            $this->status = $response->status();
            $this->response = $response->json();
        }
    }

    /**
     * Configures a reusable HTTP client with
     * the API secret and URL.
     * 
     * @return void
     */
    protected function buildHttpClient(): void
    {
        $this->client = Http::withHeaders([
            'apikey' => $this->secret
        ])
            ->timeout($this->timeout)
            ->retry($this->retryAmount, $this->retryThrottle);
    }

    /**
     * Returns the list of available houses.
     * 
     * @return App/SDK/PotterAPI/PotterAPI
     */
    public function getHouses(): PotterAPI
    {
        $endpoint = $this->apiURL . 'houses';
        $this->get($endpoint);

        return $this;
    }

    /**
     * Get a Potter API response from cache.
     * 
     * This generates a hash based on the HTTP verb and
     * endpoint in order to create a cache key.
     * 
     * TODO: This does not consider complex queries where
     * there's a body and stuff like that, this should be improved,
     * but since we're dealing with a single route on this recruitment
     * test, this will suffice for now.
     * Also, this would look better as a Http Client Middleware, but
     * those DO NOT run on the Mock calls provided by Laravel Fake method,
     * so running unit/integration tests on it would be impossible.
     * 
     * @param string $method The HTTP verb of the request.
     * @param string $endpoint The endpoint of the request.
     * 
     * @return array|null
     */
    protected function getFromCache(string $method, string $endpoint): array|null
    {
        $key = hash('sha256', "$method.$endpoint");
        $cache = Cache::store(env('CACHE_DRIVER'))->get($key);

        if ($cache !== null) {
            return json_decode($cache, true);
        }

        return null;
    }

    /**
     * Set a Potter API response on cache.
     * 
     * This generates a hash based on the HTTP verb and
     * endpoint in order to create a cache key.
     * 
     * TODO: This does not consider complex queries where
     * there's a body and stuff like that, this should be improved,
     * but since we're dealing with a single route on this recruitment
     * test, this will suffice for now.
     * Also, this would look better as a Http Client Middleware, but
     * those DO NOT run on the Mock calls provided by Laravel Fake method,
     * so running unit/integration tests on it would be impossible.
     * 
     * @param string $method The HTTP verb of the request.
     * @param string $endpoint The endpoint of the request.
     * @param Illuminate/Http/Client/Response $response The response from the API
     * 
     * @return void
     */
    protected function setResponseCache(string $method, string $endpoint, Response $response): void
    {
        $key = hash('sha256', "$method.$endpoint");
        Cache::store(env('CACHE_DRIVER'))->put($key, $response->body(), $this->cacheLifespan);
    }
}
