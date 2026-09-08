<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Tolt API Key
    |--------------------------------------------------------------------------
    |
    | Your Tolt API key, sent as a Bearer token on every request. Find it in
    | your Tolt dashboard under Settings > Integrations.
    |
    */
    'api_key' => env('TOLT_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Default Program
    |--------------------------------------------------------------------------
    |
    | Every list endpoint requires a `program_id`. Set it here to have it sent
    | automatically, or pass `program_id` explicitly in the query array of any
    | list method to override it.
    |
    */
    'program_id' => env('TOLT_PROGRAM_ID'),

    /*
    |--------------------------------------------------------------------------
    | Base URL
    |--------------------------------------------------------------------------
    |
    | The Tolt v1 REST API base URL. Override only if Tolt gives you a
    | dedicated endpoint.
    |
    */
    'base_url' => env('TOLT_BASE_URL', 'https://api.tolt.com/v1'),
];
