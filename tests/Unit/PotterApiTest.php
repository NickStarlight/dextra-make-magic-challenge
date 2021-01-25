<?php

namespace Tests\Unit;

use App\SDK\PotterAPI\PotterAPI;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * The unit testing for all Potter API SDK public methods.
 *
 * @see https://github.com/dextra/challenges/blob/master/backend/MAKE-MAGIC-PT.md
 * @author Nickolas Gomes Moraes
 * @version 1.0
 * @license WTFPL
 */
class PotterApiTest extends TestCase
{
    /**
     * Test a successful return from the houses endpoint.
     *
     * @return void
     */
    public function test_get_houses(): void
    {
        /** 
         * Builds a fake HTTP response.
         * We only care about the request status code here
         * so we know that the SDK is communicating and parsing
         * the responses in the right fashion.
         * The response body can change and I don't have any
         * documentation for maintaning that, so I won't test that.
         */
        Http::fake(function () {
            return Http::response([], 200);
        });

        $SDK = new PotterAPI();
        $response = $SDK->getHouses();
        $status = $response->status;

        $this->assertEquals($status, 200);
    }

    /**
     * Test a server error return from the houses endpoint.
     *
     * @return void
     */
    public function test_failed_get_houses(): void
    {
        Http::fake(function () {
            return Http::response(null, 500);
        });

        /** 
         * When all requests made by the retry policy fail, 
         * Illuminate/Http/Client/RequestException is raised. 
         */
        $this->expectException(RequestException::class);

        $SDK = new PotterAPI();
        $SDK->getHouses();
    }

    /**
     * Test if the houses response came from cache.
     *
     * @return void
     */
    public function test_get_houses_cache_hit(): void
    {
        Http::fake(function () {
            return Http::response([], 200);
        });

        $SDK = new PotterAPI(fromCache: true);
        $SDK->getHouses();
        $SDK->getHouses();

        Http::assertSentCount(1);
    }

    /**
     * Tests if the houses are fetched from the API and
     * not from cache when the `fromcache` parameter is false.
     * 
     * @return void
     */
    public function test_get_houses_disabled_cache(): void
    {
        Http::fake(function () {
            return Http::response([], 200);
        });

        $SDK = new PotterAPI(fromCache: false);
        $SDK->getHouses();
        $SDK->getHouses();
        $SDK->getHouses();

        Http::assertSentCount(3);
    }
}
