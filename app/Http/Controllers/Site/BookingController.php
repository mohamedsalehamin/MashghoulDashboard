<?php

namespace App\Http\Controllers\Site;

use App\CatalogModule\Models\Reservation;
use App\CatalogModule\Models\Seat;
use App\CatalogModule\Models\Transaction;
use App\ContentModule\Models\Page;
use App\DefaultPanel\Settings\GeneralSettings;
use App\DefaultPanel\Settings\LandingSettings;
use App\Http\Controllers\Controller;
use App\UsersModule\Models\Provider;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class BookingController extends Controller
{
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

    public function create(Provider $provider): \Illuminate\View\View|RedirectResponse
    {
        $provider = Provider::with('city')->enabled()->withoutTrashed()->whereHas('user')->whereKey($provider->id)->firstOrFail();

        // Cart must have items for this provider (like old_tmoono EnsureThatCartExist)
        $cartProviderId = session('cart_provider_id');
        $cart = app('cart');
        if ((int) $cartProviderId !== (int) $provider->id || $cart->getContent()->isEmpty()) {
            return redirect()
                ->route('site.provider.show', $provider)
                ->with('warning', __('site.heading.select_services_first') ?? 'يرجى اختيار الخدمات أولاً');
        }

        $reservationData = session('reservation_data', []);
        $seatId = $reservationData['seat_id'] ?? $provider->seats()->enabled()->first()?->id;
        if (! $seatId) {
            return redirect()->route('site.provider.show', $provider)
                ->with('warning', __('site.heading.select_services_first'));
        }
        $seat = Seat::with('provider')->find($seatId);
        if (! $seat || $seat->provider_id !== $provider->id) {
            return redirect()->route('site.provider.show', $provider)
                ->with('warning', __('site.heading.select_services_first'));
        }

        $cartContent = $cart->getContent()->values();
        $cartServices = $cartContent->map(function ($item) {
            $products = collect(Arr::get($item->attributes, 'products', []));
            $productsArray = $products->map(fn ($p) => [
                'id' => $p->id,
                'quantity' => $p->quantity ?? 1,
                'name' => is_array($p->title ?? null) ? ($p->getTranslation('title', app()->getLocale()) ?? '') : ($p->title ?? ''),
                'price' => $p->price->formatByDecimal(),
                'total_price' => \Cknow\Money\Money::parse((! $p->sale_price->isZero() ? $p->sale_price->formatByDecimal() : $p->price->formatByDecimal()) * ($p->quantity ?? 1))->formatByDecimal(),
            ])->values()->toArray();

            return [
                'id' => $item->associatedModel->id,
                'name' => is_array($item->associatedModel->title ?? null) ? ($item->associatedModel->getTranslation('title', app()->getLocale()) ?? '') : ($item->associatedModel->title ?? ''),
                'duration' => $item->associatedModel->duration ?? 0,
                'image' => $item->associatedModel->getFirstMediaUrl(),
                'service_price' => $item->associatedModel->price->formatByDecimal(),
                'sale_price' => ! $item->associatedModel->sale_price->isZero() ? $item->associatedModel->sale_price->formatByDecimal() : null,
                'products' => $productsArray,
            ];
        })->values()->toArray();

        $totals = $cart->formattedTotals();
        $pointsEarned = (new GeneralSettings)->getPointsOnAction('reserve') ?? 0;
        $reservationFlow = $provider->user?->options?->reservation_flow ?? 'total';
        $completeOrderText = $provider->user?->options?->texts[app()->getLocale()]['text_when_order_completed'] ?? ($provider->user?->options?->text_when_order_completed ?? '');

        $workingDays = collect($provider->meta_data['days_list'] ?? [])->where('status', 1)->values()->toArray();
        $nextDays = $this->getNext11Days($workingDays);
        $landingSettings = new LandingSettings;
        $termsPageId = $landingSettings->content['app_pages']['terms_and_conditions']
            ?? $landingSettings->content['site_pages']['terms_and_conditions']
            ?? null;
        $termsPage = $termsPageId ? Page::find($termsPageId) : null;
        $termsUrl = $termsPage ? route('site.page', $termsPage->getTranslation('slug', app()->getLocale()) ?: 'terms') : '#';

        return view('site.new.booking-create', array_merge($this->sharedData(), [
            'provider' => $provider,
            'seat' => [
                'id' => $seat->id,
                'title' => $seat->getTranslation('title', app()->getLocale()),
            ],
            'cartServices' => $cartServices,
            'totals' => $totals,
            'pointsEarned' => $pointsEarned,
            'reservationFlow' => $reservationFlow,
            'completeOrderText' => $completeOrderText,
            'nextDays' => $nextDays,
            'workingDays' => $workingDays,
            'termsUrl' => $termsUrl,
        ]));
    }

    public function completedSuccess()
    {
        return $this->completed('success');
    }

    public function completedFailed()
    {
        return $this->completed('failed');
    }

    protected function completed(string $status)
    {
        $orderId = session('reservation_id');

        return view('site.new.booking-completed', array_merge($this->sharedData(), [
            'status' => $status,
            'order_id' => $orderId,
        ]));
    }

    public function checkoutError(Request $request)
    {
        $reason = $request->query('reason');
        $providerId = $request->query('provider_id');

        if (in_array($reason, ['tabby_cancel', 'tabby_failure'], true)) {
            $messageKey = $reason === 'tabby_cancel' ? 'validation.tabby.redirect_cancel' : 'validation.tabby.redirect_failure';

            return view('site.new.booking-checkout-error', array_merge($this->sharedData(), [
                'tabbyReason' => $reason,
                'tabbyMessage' => __($messageKey),
                'showRetryCheckout' => true,
                'retryUrl' => $providerId ? route('site.booking.create', $providerId) : null,
            ]));
        }

        return view('site.new.booking-checkout-error', array_merge($this->sharedData(), [
            'tabbyReason' => null,
            'tabbyMessage' => null,
            'showRetryCheckout' => false,
            'retryUrl' => null,
        ]));
    }

    protected function getNext11Days(array $workingDays): array
    {
        $days = [];
        $locale = app()->getLocale();
        for ($i = 0; $i < 11; $i++) {
            $date = Carbon::today()->addDays($i);
            $dayName = strtolower($date->format('l'));
            $disable = collect($workingDays)->where('day_name', $dayName)->where('status', 1)->isEmpty();
            $days[] = [
                'title' => __('forms.fields.weekdays.'.$dayName),
                'date' => $date->format('Y-m-d'),
                'dateText' => $date->format('m-d'),
                'disable' => $disable,
            ];
        }

        return $days;
    }

    public function show(int $reservation)
    {
        $reservation = Reservation::with(['reservable.city', 'seat', 'itemsLine', 'conditions', 'rates', 'transaction', 'transactions'])
            ->where('user_id', auth()->guard('site')->id())
            ->findOrFail($reservation);

        $totals = [];
        $servicesList = [];
        $totalDuration = 0;
        try {
            $cart = $reservation->as_cart;
            $totals = $cart->formattedTotals();
            $content = $cart->getContent();
            foreach ($content as $item) {
                $model = $item->associatedModel ?? null;
                $name = $model ? (is_array($model->title ?? null) ? ($model->getTranslation('title', app()->getLocale()) ?? '') : ($model->title ?? '')) : ($item->name ?? '—');
                $duration = $model->duration ?? 0;
                $totalDuration += $duration * ($item->quantity ?? 1);
                $price = method_exists($item, 'getPriceSumWithConditions') ? $item->getPriceSumWithConditions(true) : 0;
                $servicesList[] = [
                    'name' => $name.($duration ? ' ('.$duration.' '.__('site.minutes').')' : ''),
                    'duration' => $duration,
                    'quantity' => $item->quantity ?? 1,
                    'price' => $price ? \Cknow\Money\Money::parse($price)->formatByDecimal() : '—',
                ];
                $products = collect(Arr::get($item->attributes ?? [], 'products', []));
                foreach ($products as $product) {
                    $pName = is_object($product) ? (is_array($product->title ?? null) ? ($product->getTranslation('title', app()->getLocale()) ?? '') : ($product->title ?? '')) : '—';
                    $servicesList[] = [
                        'name' => $pName,
                        'duration' => 0,
                        'quantity' => $product->quantity ?? 1,
                        'price' => isset($product->total_price) ? $product->total_price : (is_object($product) && $product->price ? \Cknow\Money\Money::parse($product->price->getAmount() * ($product->quantity ?? 1))->formatByDecimal() : '—'),
                    ];
                }
            }
        } catch (\Throwable $e) {
            $priceFormatted = $reservation->price->formatByDecimal();
            $totals = [
                'services_total' => $priceFormatted,
                'products_total' => '0.00',
                'discount' => '0.00',
                'subtotal' => $priceFormatted,
                'reservation_fees' => '0.00',
                'total' => $priceFormatted,
            ];
        }

        $provider = $reservation->reservable;
        $workingDays = collect($provider->meta_data['days_list'] ?? [])->where('status', 1)->values()->toArray();
        $daysLabels = [
            'saturday' => __('forms.fields.weekdays.saturday'),
            'sunday' => __('forms.fields.weekdays.sunday'),
            'monday' => __('forms.fields.weekdays.monday'),
            'tuesday' => __('forms.fields.weekdays.tuesday'),
            'wednesday' => __('forms.fields.weekdays.wednesday'),
            'thursday' => __('forms.fields.weekdays.thursday'),
            'friday' => __('forms.fields.weekdays.friday'),
        ];
        $workingHoursText = collect($workingDays)->map(function ($day) use ($daysLabels) {
            $dayName = $daysLabels[$day['day_name'] ?? ''] ?? $day['day_name'] ?? '';

            return $dayName.': '.($day['from'] ?? '').' - '.($day['to'] ?? '');
        })->implode(' | ');

        $workingDaysList = collect($workingDays)->map(function ($day) use ($daysLabels) {
            $dayName = $daysLabels[$day['day_name'] ?? ''] ?? $day['day_name'] ?? '';

            return $dayName.': '.($day['from'] ?? '').' - '.($day['to'] ?? '');
        })->values()->all();

        $paymentMethod = '—';
        $paidAmount = $reservation->price->formatByDecimal();
        if ($reservation->transaction) {
            $gateway = $reservation->transaction->meta_data['gateway'] ?? $reservation->transaction->meta_data['method'] ?? null;
            $paymentMethod = $gateway ? __('panel.gateways.'.$gateway) : ($reservation->transaction->meta_data['method'] ?? '—');
        }

        $pointsEarned = (int) ($reservation->meta_data['points'] ?? (new GeneralSettings)->getPointsOnAction('reserve') ?? 0);

        $serviceRating = $reservation->rates()->where('type', 'service')->first();
        $placeRating = $reservation->rates()->where('type', 'place')->first();

        $pendingMyfatoorahPaymentUrl = $this->pendingMyfatoorahPaymentUrl($reservation);

        return view('site.new.booking-details', array_merge($this->sharedData(), [
            'reservation' => $reservation,
            'totals' => $totals,
            'servicesList' => $servicesList,
            'totalDuration' => $totalDuration,
            'workingHoursText' => $workingHoursText,
            'workingDaysList' => $workingDaysList,
            'paymentMethod' => $paymentMethod,
            'paidAmount' => $paidAmount,
            'pointsEarned' => $pointsEarned,
            'canRate' => $reservation->canRate(),
            'serviceRating' => $serviceRating,
            'placeRating' => $placeRating,
            'pendingMyfatoorahPaymentUrl' => $pendingMyfatoorahPaymentUrl,
        ]));
    }

    /**
     * First MyFatoorah transaction without paid_at (same rule as customer app ReservationDetails).
     */
    protected function pendingMyfatoorahPaymentUrl(Reservation $reservation): ?string
    {
        /** @var \Illuminate\Support\Collection<int, Transaction> $transactions */
        $transactions = $reservation->relationLoaded('transactions')
            ? $reservation->transactions
            : $reservation->transactions()->get();

        foreach ($transactions as $transaction) {
            $meta = $transaction->meta_data ?? [];
            $gateway = $meta['gateway'] ?? $meta['method'] ?? null;
            if ($gateway !== 'myfatoorah') {
                continue;
            }
            if (! empty($meta['paid_at'])) {
                continue;
            }
            $url = $meta['invoiceURL'] ?? $meta['InvoiceURL'] ?? $meta['invoice_url'] ?? $meta['PaymentURL'] ?? $meta['paymentURL'] ?? null;
            if (is_string($url) && filter_var($url, FILTER_VALIDATE_URL)) {
                return $url;
            }
        }

        return null;
    }
}
