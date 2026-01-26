<?php

return [
    'version' => env('API_VERSION', 'v2'),
    'uri_prefix' => env('API_URI_PREFIX', 'Api'),
    'debug' => env('API_DEBUG', false),
    'resources_map' => [
        'items' => 'Tasawk\Items',
        'contact' => 'Tasawk\Contact',
    ]


];
