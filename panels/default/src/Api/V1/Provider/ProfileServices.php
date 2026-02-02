<?php

namespace App\DefaultPanel\Api\V1\Provider;


use Api;
use App\CatalogModule\Models\Reservation\Rate;
use App\DefaultPanel\Actions\Shared\Authentication\UpdateUserPassword;
use App\DefaultPanel\Requests\Api\Provider\UpdatePasswordRequest;
use App\DefaultPanel\Resources\Api\Provider\ProviderAccountResources;
use App\UsersModule\Models\Provider as ProviderModel;
use Illuminate\Http\Request;


class ProfileServices {

    public function index() {
        return Api::isOk(__("Provider information"), ProviderAccountResources::make(auth()->user()));
    }

    public function updatePassword(UpdatePasswordRequest $request) {
        UpdateUserPassword::run(auth()->user(), $request->get('password'));
        return Api::isOk(__("Account information updated"), ProviderAccountResources::make(auth()->user()));
    }

    public function rates() {
        $provider = provider();

        if (!$provider) {
            $msg = __('panel.provider_not_found');
            return Api::isError($msg === 'panel.provider_not_found' ? 'Provider not found' : $msg, 404);
        }

        $allRatings = Rate::query()
            ->where(function ($query) use ($provider) {
                // Reservation-based ratings
                $query->whereHas('reservation', function ($q) use ($provider) {
                    $q->where('reservable_type', ProviderModel::class)
                        ->where('reservable_id', $provider->id);
                })
                // OR manual ratings for this provider
                ->orWhere(function ($q) use ($provider) {
                    $q->where('provider_id', $provider->user_id)
                        ->where('source', 'manual');
                });
            })
            ->whereNull('parent_id')
            ->where('is_approved', true)
            ->with(['user', 'reservation.customer', 'replies.user'])
            ->latest()
            ->get();

        $grouped = $allRatings->groupBy(function ($rate) {
            return $rate->pair_id ?? $rate->reservation_id ?? 'single_' . $rate->id;
        });

        $rates = $grouped->map(function ($group) {
            $serviceRating = $group->firstWhere('type', 'service');
            $placeRating = $group->firstWhere('type', 'place');
            $baseRating = $serviceRating ?: $placeRating ?: $group->first();

            // Collect replies from both ratings (service/place)
            $allReplies = collect();
            if ($serviceRating && $serviceRating->relationLoaded('replies')) {
                $allReplies = $allReplies->merge($serviceRating->replies);
            }
            if ($placeRating && $placeRating->relationLoaded('replies')) {
                $allReplies = $allReplies->merge($placeRating->replies);
            }

            $replies = $allReplies->unique('id')
                ->sortBy('created_at')
                ->map(function ($reply) {
                    return [
                        'id' => $reply->id,
                        'comment' => $reply->comment,
                        'created_at' => $reply->created_at?->diffForHumans(),
                        'user' => $reply->user?->name ?? __('panel.provider'),
                    ];
                })
                ->values()
                ->toArray();

            return [
                'id' => $baseRating->id,
                'name' => $baseRating->user?->name ?? $baseRating->reservation?->customer?->name ?? __('panel.anonymous'),
                'created_at' => $baseRating->created_at?->diffForHumans(),
                'service' => $serviceRating ? [
                    'rate' => (int) $serviceRating->rate,
                    'comment' => $serviceRating->comment,
                ] : null,
                'place' => $placeRating ? [
                    'rate' => (int) $placeRating->rate,
                    'comment' => $placeRating->comment,
                ] : null,
                'replies' => $replies,
            ];
        })->values()->toArray();

        // Ensure it's a plain indexed array without keys
        $rates = array_values($rates);

        $avg = $allRatings->whereNotNull('rate')->avg('rate') ?? 0;

        // Use Collection to preserve array structure (like AnonymousResourceCollection does)
        // Collections implement JsonSerializable and will be encoded as arrays
        $ratesCollection = collect($rates);

        return Api::isOk(__("Provider rates"), $ratesCollection)
            ->addAttribute('avg_rates', (float) $avg)
            ->addAttribute('rates_count', (int) $grouped->count());
    }

    /**
     * Reply to a reservation/manual rate (only one reply per rating group).
     */
    public function reply(Request $request, Rate $rate)
    {
        $provider = provider();
        if (!$provider) {
            $msg = __('panel.provider_not_found');
            return Api::isError($msg === 'panel.provider_not_found' ? 'Provider not found' : $msg, 404);
        }

        $data = $request->validate([
            'comment' => ['required', 'string', 'max:512'],
        ]);

        // Always reply to the top-level rating
        if ($rate->parent_id) {
            $rate = $rate->parent()->firstOrFail();
        }

        // Authorization: must belong to this provider (manual or reservation-based)
        $belongs =
            (($rate->provider_id !== null) && ((int) $rate->provider_id === (int) $provider->user_id)) ||
            ($rate->reservation && $rate->reservation->reservable_type === ProviderModel::class && (int) $rate->reservation->reservable_id === (int) $provider->id);

        if (!$belongs) {
            $msg = __('panel.not_allowed');
            return Api::isError($msg === 'panel.not_allowed' ? 'Not allowed' : $msg, 403);
        }

        // Find the rating group (pair_id preferred, else reservation_id)
        $groupQuery = Rate::query()->whereNull('parent_id');
        if ($rate->pair_id) {
            $groupQuery->where('pair_id', $rate->pair_id);
        } elseif ($rate->reservation_id) {
            $groupQuery->where('reservation_id', $rate->reservation_id);
        } else {
            $groupQuery->whereKey($rate->id);
        }

        $groupRatings = $groupQuery->get();
        $groupIds = $groupRatings->pluck('id')->all();

        // Only one reply per group
        $alreadyReplied = Rate::query()
            ->where('source', Rate::SOURCE_REPLY)
            ->whereIn('parent_id', $groupIds)
            ->exists();

        if ($alreadyReplied) {
            $msg = __('panel.reply_already_exists');
            return Api::isError($msg === 'panel.reply_already_exists' ? 'Reply already exists' : $msg, 422);
        }

        // Prefer replying on the service rating (keeps UI consistent)
        $parentForReply = $groupRatings->firstWhere('type', 'service') ?? $rate;
        $reply = $parentForReply->createReply($data['comment'], auth()->id());

        $msg = __('panel.reply_added_successfully');
        return Api::isOk($msg === 'panel.reply_added_successfully' ? 'Reply added successfully' : $msg, [
            'id' => $reply->id,
            'comment' => $reply->comment,
            'created_at' => $reply->created_at?->diffForHumans(),
        ]);
    }
}
