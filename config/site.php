<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Provider discovery radius (website + parity with mobile)
    |--------------------------------------------------------------------------
    |
    | Mashghoul mobile sends `distance` in km on /providers (see client api.ts).
    | Providers farther than this from the visitor's saved coordinates are
    | excluded when location is set. Override with PROVIDER_SEARCH_RADIUS_KM.
    |
    */
    'provider_search_radius_km' => (int) env('PROVIDER_SEARCH_RADIUS_KM', 180),

];
