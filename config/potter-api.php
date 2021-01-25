<?php

return [
    /*
    |--------------------------------------------------------------------------
    | The Potter API URL
    |--------------------------------------------------------------------------
    |
    | This should be provided by whoever is applying the test and may 
    | change.
    |
    */
    'POTTER_API_URL' => env('POTTER_API_URL'),

    /*
    |--------------------------------------------------------------------------
    | The Potter API Secret
    |--------------------------------------------------------------------------
    |
    | You should generate a secret manually on the `potterApi/users` endpoint
    | in order to use any other endpoint.
    |
    */
    'POTTER_API_SECRET' => env('POTTER_API_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Failed HTTP requests retry policy
    |--------------------------------------------------------------------------
    |
    | This defines how many times any failed HTTP requests made to the Potter API 
    | should be retried.
    |
    */
    'POTTER_API_RETRY_COUNT' => env('POTTER_API_RETRY_COUNT', 0),

    /*
    |--------------------------------------------------------------------------
    | Cache policy
    |--------------------------------------------------------------------------
    |
    | Sets the lifespan of Potter API requests stored on cache.
    | If this value is set to 0, no cache engine will be used.
    |
    */
    'POTTER_API_CACHE_LIFESPAN' => env('POTTER_API_CACHE_LIFESPAN', 1800),
];
