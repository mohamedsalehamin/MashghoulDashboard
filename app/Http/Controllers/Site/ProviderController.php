<?php

namespace App\Http\Controllers\Site;

use App\CatalogModule\Models\Reservation\Rate;
use App\ContentModule\Models\Coupon;
use App\ContentModule\Models\Page;
use App\DefaultPanel\Actions\AddProviderCartAction;
use App\DefaultPanel\Enum\CouponTypes;
use App\DefaultPanel\Settings\GeneralSettings;
use App\DefaultPanel\Settings\LandingSettings;
use App\Http\Controllers\Controller;
use App\Http\Requests\Site\AddToCartRequest;
use App\Support\PortfolioAlbumPresentation;
use App\Support\ProviderListingRadius;
use App\UsersModule\Models\Provider;
use Carbon\Carbon;
use Cknow\Money\Money;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use MatanYadaev\EloquentSpatial\Objects\Point;

class ProviderController extends Controller
{
    protected function userLocationIsSet(): bool
    {
        return session()->has('location_set')
            && session('location_set') === true
            && session()->has('user_latitude')
            && session()->has('user_longitude');
    }

    protected function sharedData(): array
    {
        $settings = new GeneralSettings;
        $landingSettings = new LandingSettings;
        $appPages = $landingSettings->content['app_pages'] ?? [];
        $pages = collect($appPages)->mapWithKeys(function ($pageId, $pageName) {
            return [$pageName => Page::find($pageId)];
        })->filter();

        return [
            'settings' => $settings,
            'landingSettings' => $landingSettings,
            'pages' => $pages,
        ];
    }

    public function show(int $provider)
    {
        // Clear cart when navigating to a different provider (like old_tmoono Lab clears on mount)
        $cartProviderId = session('cart_provider_id');
        if ($cartProviderId !== null && (int) $cartProviderId !== (int) $provider) {
            app('cart')->clear();
            session()->forget(['cart_provider_id', 'reservation_data']);
        }

        $user_location = [
            'lat' => (float) session('user_latitude', request()->get('latitude', 0)),
            'lng' => (float) session('user_longitude', request()->get('longitude', 0)),
        ];
        $point = new Point($user_location['lat'], $user_location['lng']);

        $provider = Provider::withDistanceSphere('location', $point)
            ->enabled()
            ->withoutTrashed()
            ->whereHas('user')
            ->with([
                'seats' => fn ($q) => $q->enabled()->with([
                    'serviceGroups' => fn ($g) => $g->orderBy('sort')->orderBy('id'),
                    'services' => fn ($s) => $s->enabled()->with('products'),
                ]),
            ])
            ->findOrFail($provider);

        $locale = app()->getLocale();
        $title = $provider->getTranslation('name', $locale);
        $metaDescription = $provider->getTranslation('meta_description', $locale)
            ?: Str::limit(strip_tags((string) $provider->getTranslation('bio', $locale)), 160);
        $metaKeywords = $provider->getTranslation('meta_keywords', $locale) ?: '';
        $metaKeywords = is_array($metaKeywords) ? implode(', ', $metaKeywords) : (string) $metaKeywords;

        $seats = $provider->seats->map(function ($seat) {
            $services = $seat->services->map(function ($svc) {
                $svc->pivot_service_group_id = $svc->pivot?->service_group_id;

                return $svc;
            });

            return [
                'id' => $seat->id,
                'title' => $seat->getTranslation('title', app()->getLocale()),
                'service_groups' => $seat->serviceGroups->map(fn ($g) => [
                    'id' => $g->id,
                    'title' => $g->getTranslation('title', app()->getLocale()),
                ])->values()->all(),
                'services' => $services->values()->all(),
            ];
        })->values();

        $workingDays = collect($provider->meta_data['days_list'] ?? [])
            ->where('status', 1)
            ->map(fn ($slot) => [
                'day_name' => $slot['day_name'] ?? null,
                'day' => isset($slot['day_name']) ? __('forms.fields.weekdays.'.$slot['day_name']) : '',
                'from' => ! empty($slot['from']) ? Carbon::parse($slot['from'])->locale(app()->getLocale())->translatedFormat('h:i A') : '',
                'to' => ! empty($slot['to']) ? Carbon::parse($slot['to'])->locale(app()->getLocale())->translatedFormat('h:i A') : '',
            ])
            ->filter(fn ($s) => $s['day_name'])
            ->values();

        $availableCoupons = $this->getActiveCoupons($provider);
        $latestRates = $this->getGroupedRates($provider);
        $portfolio = $this->getPortfolioAlbums($provider);

        $siteUserId = site()->user()?->id;
        $isFavorited = $siteUserId ? $provider->isFavorited($siteUserId) : false;
        $shareLink = route('site.provider.show', $provider->id);

        return view('site.new.provider-show', array_merge($this->sharedData(), [
            'provider' => $provider,
            'seats' => $seats,
            'workingDays' => $workingDays,
            'availableCoupons' => $availableCoupons,
            'latestRates' => $latestRates,
            'portfolio' => $portfolio,
            'isFavorited' => $isFavorited,
            'shareLink' => $shareLink,
            'title' => $title,
            'metaDescription' => $metaDescription,
            'metaKeywords' => $metaKeywords,
        ]));
    }

    protected function getActiveCoupons(Provider $provider): array
    {
        $couponIds = Coupon::listingIdsForProvider($provider->id, includeGeneralScope: false);

        if ($couponIds->isEmpty()) {
            return [];
        }

        return Coupon::query()
            ->whereIn('id', $couponIds)
            ->where('status', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->get()
            ->map(fn ($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'code' => $c->code,
                'discount_type' => $c->discount_type->value,
                'discount_value' => $c->discount_value,
                'formatted_value' => $c->formattedValue(),
                'display_value' => $this->couponListingDisplayValue($c),
                'end_date' => $c->end_date?->format('d/m/Y'),
                'applies_to' => $c->appliesToLabel(),
                'min_order_amount' => $c->minOrderAmountFormatted(),
                'min_order_type_label' => $c->minOrderTypeLabel(),
            ])
            ->toArray();
    }

    /**
     * Coupon discount text for provider listing: ASCII digits only (no Arabic numerals / ر.س.) — riyal icon is in the view for fixed amounts.
     */
    protected function couponListingDisplayValue(Coupon $c): string
    {
        if ($c->discount_type === CouponTypes::PERCENTAGE) {
            return number_format((float) $c->discount_value, 0).'%';
        }

        $amount = (float) Money::parse($c->discount_value)->formatByDecimal();

        return number_format($amount, $amount === floor($amount) ? 0 : 2);
    }

    protected function getPortfolioAlbums(Provider $provider): array
    {
        return PortfolioAlbumPresentation::forProvider($provider);
    }

    protected function getGroupedRates(Provider $provider): array
    {
        $allRatings = Rate::where(function ($q) use ($provider) {
            $q->whereHas('reservation', fn ($r) => $r->where('reservable_type', Provider::class)->where('reservable_id', $provider->id))
                ->orWhere(fn ($r) => $r->where('provider_id', $provider->user_id)->where('source', 'manual'));
        })
            ->whereNull('parent_id')
            ->where('is_approved', true)
            ->with(['user', 'reservation.customer', 'replies.user'])
            ->latest()
            ->get();

        $grouped = $allRatings->groupBy(fn ($r) => $r->pair_id ?? $r->reservation_id ?? 'single_'.$r->id);

        return $grouped->take(10)->map(function ($group) {
            $serviceRating = $group->firstWhere('type', 'service');
            $placeRating = $group->firstWhere('type', 'place');
            $base = $serviceRating ?: $placeRating ?: $group->first();
            $allReplies = collect();
            if ($serviceRating?->relationLoaded('replies')) {
                $allReplies = $allReplies->merge($serviceRating->replies);
            }
            if ($placeRating?->relationLoaded('replies')) {
                $allReplies = $allReplies->merge($placeRating->replies);
            }
            $replies = $allReplies->unique('id')->sortBy('created_at')->map(fn ($r) => [
                'comment' => $r->comment,
                'created_at' => $r->created_at?->diffForHumans(),
                'user' => $r->user?->name ?? __('panel.provider'),
            ])->values()->toArray();

            return [
                'name' => $base->user?->name ?? $base->reservation?->customer?->name ?? __('panel.anonymous'),
                'created_at' => $base->created_at?->diffForHumans(),
                'service' => $serviceRating ? ['rate' => (int) $serviceRating->rate, 'comment' => $serviceRating->comment] : null,
                'place' => $placeRating ? ['rate' => (int) $placeRating->rate, 'comment' => $placeRating->comment] : null,
                'replies' => $replies,
            ];
        })->values()->toArray();
    }

    public function gallery(int $provider)
    {
        $provider = Provider::enabled()
            ->withoutTrashed()
            ->whereHas('user')
            ->findOrFail($provider);

        $portfolio = $this->getPortfolioAlbums($provider);
        $locale = app()->getLocale();
        $providerName = $provider->getTranslation('name', $locale);

        return view('site.new.provider-gallery', array_merge($this->sharedData(), [
            'provider' => $provider,
            'providerName' => $providerName,
            'portfolio' => $portfolio,
        ]));
    }

    public function map(int $provider)
    {
        $provider = Provider::enabled()
            ->withoutTrashed()
            ->whereHas('user')
            ->findOrFail($provider);

        $location = $provider->location;
        if (! $location) {
            abort(404, __('site.no_location') ?: 'Provider location is not set.');
        }
        $coords = $location->getCoordinates();
        $lat = (float) $coords[1];
        $lng = (float) $coords[0];

        return view('site.new.provider-map', array_merge($this->sharedData(), [
            'provider' => $provider,
            'lat' => $lat,
            'lng' => $lng,
        ]));
    }

    /**
     * Paginated list of providers ordered by rating (highest first), like mobile app.
     */
    public function mostRated()
    {
        $userLat = (float) session('user_latitude', request()->get('latitude', 0));
        $userLng = (float) session('user_longitude', request()->get('longitude', 0));
        $point = new Point($userLat, $userLng);

        $providers = Provider::enabled()
            ->withoutTrashed()
            ->whereHas('user')
            ->when($this->userLocationIsSet(), fn ($q) => $q->whereDistanceSphere('location', $point, '<=', ProviderListingRadius::maxDistanceMeters()))
            ->withDistanceSphere('location', $point)
            ->withAvg('rate', 'rate')
            ->orderBy('rate_avg_rate', 'desc')
            ->paginate(12)
            ->withQueryString();

        return view('site.new.providers-list', array_merge($this->sharedData(), [
            'providers' => $providers,
            'listType' => 'most_rated',
            'pageTitle' => __('site.heading.most_rated'),
        ]));
    }

    /**
     * Paginated list of providers ordered by distance from visitor's saved location, like mobile app.
     */
    public function nearest()
    {
        $userLat = (float) session('user_latitude', request()->get('latitude', 0));
        $userLng = (float) session('user_longitude', request()->get('longitude', 0));
        $point = new Point($userLat, $userLng);

        $providers = Provider::enabled()
            ->withoutTrashed()
            ->whereHas('user')
            ->when($this->userLocationIsSet(), fn ($q) => $q->whereDistanceSphere('location', $point, '<=', ProviderListingRadius::maxDistanceMeters()))
            ->withDistanceSphere('location', $point)
            ->withAvg('rate', 'rate')
            ->orderBy('distance', 'asc')
            ->paginate(12)
            ->withQueryString();

        return view('site.new.providers-list', array_merge($this->sharedData(), [
            'providers' => $providers,
            'listType' => 'nearest',
            'pageTitle' => __('site.heading.nearest_to_you'),
        ]));
    }

    /**
     * Add selected services (and products) to cart, then redirect to booking page.
     * Services must be added to cart before going to reservation (like old_tmoono).
     */
    public function addToCart(AddToCartRequest $request, Provider $provider): RedirectResponse
    {
        AddProviderCartAction::run($provider, $request->validated());

        session(['reservation_data' => [
            'provider' => $provider->id,
            'seat_id' => $request->validated('seat_id'),
        ]]);

        return redirect()->route('site.booking.create', $provider->id);
    }

    public function toggleFavorite(Provider $provider): JsonResponse
    {
        $user = site()->user();
        Auth::setUser($user);
        $provider->toggleFavorite($user->id);
        $isFavorited = $provider->isFavorited($user->id);

        return response()->json([
            'success' => true,
            'favorited' => $isFavorited,
            'message' => $isFavorited
                ? __('site.heading.added_to_favorite_list')
                : __('site.heading.removed_to_favorite_list'),
        ]);
    }
}
