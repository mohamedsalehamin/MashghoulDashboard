<?php

namespace App\Support;

final class ProviderListingRadius
{
    /**
     * Maximum distance from the user's point to include a provider (meters).
     * Default km matches Mashghoul mobile `api.ts` (distance: 180).
     */
    public static function maxDistanceMeters(): int
    {
        $km = (int) config('site.provider_search_radius_km', 180);

        return max(1, $km) * 1000;
    }
}
