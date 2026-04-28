<?php

namespace App\Livewire\Site;

use App\CatalogModule\Models\Reservation;
use App\CatalogModule\Models\Seat;
use App\CatalogModule\Models\Service;
use App\DefaultPanel\Actions\BuildCartForWebCheckoutAction;
use App\DefaultPanel\Actions\CancelReservationOnPaymentFailureAction;
use App\DefaultPanel\Actions\OrderPaidAction;
use App\DefaultPanel\Enum\ReservationStatus;
use App\DefaultPanel\Settings\GeneralSettings;
use App\UsersModule\Models\Provider;
use Carbon\Carbon;
use Livewire\Component;

class BookingCheckoutForm extends Component
{
    public $provider;

    public $seat;

    public $cartServices;

    public $totals;

    public $pointsEarned;

    public $reservationFlow;

    public $completeOrderText;

    public $nextDays;

    public $workingDays;

    public string $termsUrl = '#';

    public string $date = '';

    public string $timeFrom = '';

    public string $timeTo = '';

    public $selectedTimeIndex = null;

    public string $paymentMethod = 'myfatoorah';

    public string $couponCode = '';

    public string $points = '';

    public string $wallet = '';

    public bool $approveTerms = false;

    public array $availableTimes = [];

    public bool $dateLoader = false;

    public bool $checkoutLoader = false;

    public ?string $couponError = null;

    public ?string $pointsError = null;

    public ?string $walletError = null;

    public bool $couponApplied = false;

    public bool $pointsApplied = false;

    public bool $walletApplied = false;

    public ?string $tabbyError = null;

    public ?int $userPointsBalance = null;

    public ?float $userWalletBalance = null;

    protected $listeners = ['refreshTotals' => 'refreshCartTotals'];

    public function mount($provider, $seat, $cartServices, $totals, $pointsEarned, $reservationFlow, $completeOrderText, $nextDays, $workingDays, $termsUrl = '#')
    {
        $this->provider = $provider;
        $this->seat = $seat;
        $this->cartServices = $cartServices;
        $this->totals = $totals;
        $this->pointsEarned = $pointsEarned;
        $this->reservationFlow = $reservationFlow;
        $this->completeOrderText = $completeOrderText;
        $this->nextDays = $nextDays;
        $this->workingDays = $workingDays;
        $this->termsUrl = $termsUrl;
        $this->approveTerms = false;

        $user = auth()->guard('site')->user();
        if ($user) {
            // Loyalty balance is sum of non-transferred `points` rows (same as rewards page), not PointsExchange credits.
            $this->userPointsBalance = (int) ($user->getTotalPointsBalance() ?? 0);
            $this->userWalletBalance = (float) ($user->balance ?? 0);
        }

        $this->syncDiscountInputsFromSessionCart();
    }

    /**
     * After a full page reload Livewire resets coupon/points/wallet fields, but the session cart
     * may still hold applied conditions from the previous request — hydrate inputs and flags.
     */
    protected function syncDiscountInputsFromSessionCart(): void
    {
        if ((int) session('cart_provider_id') !== (int) $this->getProviderModel()->id) {
            return;
        }

        $cart = app('cart');

        $coupon = $cart->getConditionsByType('coupon')->first();
        if ($coupon) {
            $this->couponCode = (string) $coupon->getName();
            $this->couponApplied = true;
        }

        $walletCond = $cart->getConditionsByType('wallet')->first();
        if ($walletCond) {
            $this->wallet = (string) $this->positiveConditionAmount($walletCond->getValue());
            $this->walletApplied = true;
        }

        $pointsCond = $cart->getConditionsByType('points')->first();
        if ($pointsCond) {
            $this->points = (string) $this->positiveConditionAmount($pointsCond->getValue());
            $this->pointsApplied = true;
        }
    }

    protected function positiveConditionAmount(mixed $value): float
    {
        $n = (float) preg_replace('/[^\d.-]/', '', (string) $value);

        return abs($n);
    }

    public function updatedDate($value)
    {
        if (empty($value)) {
            $this->availableTimes = [];
            $this->timeFrom = '';
            $this->timeTo = '';
            $this->selectedTimeIndex = null;

            return;
        }
        $this->selectedTimeIndex = null;
        $this->loadAvailableTimes();
    }

    public function updatedSelectedTimeIndex($value)
    {
        if ($value === null || $value === '') {
            $this->timeFrom = '';
            $this->timeTo = '';

            return;
        }
        $index = (int) $value;
        $slot = $this->availableTimes[$index] ?? null;
        if ($slot && empty($slot['reserved'] ?? false)) {
            $this->timeFrom = trim($slot['from'] ?? '');
            $this->timeTo = trim($slot['to'] ?? '');
        }
    }

    public function loadAvailableTimes()
    {
        if (empty($this->date)) {
            return;
        }
        $this->dateLoader = true;
        $this->availableTimes = [];

        try {
            $seat = Seat::find($this->seat['id']);
            if (! $seat) {
                $this->dateLoader = false;

                return;
            }
            $serviceIds = collect($this->cartServices)->pluck('id')->toArray();
            $interval = Service::findMany($serviceIds)->sum('duration') ?: 60;
            $date = Carbon::parse($this->date);
            $periods = $seat->getAvailablePeriodsOnDate($date, true, $interval);
            $this->availableTimes = $periods->flatten(1)->values()->toArray();
        } catch (\Throwable $e) {
            //
        }
        $this->dateLoader = false;
    }

    public function selectTime($index)
    {
        $index = (int) $index;
        $slot = $this->availableTimes[$index] ?? null;
        if ($slot && empty($slot['reserved'] ?? false)) {
            $this->selectedTimeIndex = $index;
            $this->timeFrom = trim($slot['from'] ?? '');
            $this->timeTo = trim($slot['to'] ?? '');
        }
    }

    public function applyCoupon()
    {
        $this->resetDiscountErrors();
        $this->couponApplied = false;
        $this->applyDiscounts();
    }

    public function removeCoupon()
    {
        $this->couponCode = '';
        $this->couponApplied = false;
        $this->resetDiscountErrors();
        $this->applyDiscounts();
    }

    public function applyPoints()
    {
        $this->resetDiscountErrors();
        $this->pointsApplied = false;
        $this->applyDiscounts();
    }

    public function removePoints()
    {
        $this->points = '';
        $this->pointsApplied = false;
        $this->resetDiscountErrors();
        $this->applyDiscounts();
    }

    public function applyWallet()
    {
        $this->resetDiscountErrors();
        $this->walletApplied = false;
        $this->applyDiscounts();
    }

    public function removeWallet()
    {
        $this->wallet = '';
        $this->walletApplied = false;
        $this->resetDiscountErrors();
        $this->applyDiscounts();
    }

    protected function resetDiscountErrors(): void
    {
        $this->couponError = null;
        $this->pointsError = null;
        $this->walletError = null;
    }

    protected function applyDiscounts(): void
    {
        $data = $this->buildCheckoutData();
        $providerModel = $this->getProviderModel();
        try {
            $cart = (new BuildCartForWebCheckoutAction)->handle($providerModel, $data);
            $this->totals = $cart->formattedTotals();
            $this->couponApplied = ! empty(trim($this->couponCode)) && $cart->getConditionsByType('coupon')->count() > 0;
            $this->pointsApplied = $cart->getConditionsByType('points')->count() > 0;
            $this->walletApplied = $cart->getConditionsByType('wallet')->count() > 0;
            if (! empty(trim($this->couponCode)) && ! $this->couponApplied) {
                $this->couponError = $cart->getLastCouponFailureMessage()
                    ?? __('validation.api.coupon_code_not_found');
            }
        } catch (\Throwable $e) {
            $msg = $e->getMessage();
            if (str_contains(strtolower($msg), 'coupon') || str_contains($msg, 'كود') || str_contains($msg, 'خصم')) {
                $this->couponError = $msg;
            } elseif (str_contains(strtolower($msg), 'point') || str_contains($msg, 'نقاط')) {
                $this->pointsError = $msg;
            } elseif (str_contains(strtolower($msg), 'wallet') || str_contains($msg, 'محفظة') || str_contains($msg, 'رصيد')) {
                $this->walletError = $msg;
            } else {
                $this->couponError = $msg;
            }
        }
    }

    public function refreshCartTotals()
    {
        $cart = app('cart');
        $this->totals = $cart->formattedTotals();
        $this->pointsApplied = $cart->getConditionsByType('points')->count() > 0;
        $this->walletApplied = $cart->getConditionsByType('wallet')->count() > 0;
    }

    public function checkout()
    {
        $this->validate([
            'date' => 'required|date',
            'timeFrom' => 'required|string',
            'timeTo' => 'required|string',
            'approveTerms' => 'accepted',
        ], [
            'date.required' => __('validation.required', ['attribute' => __('site.fields.date')]),
            'approveTerms.accepted' => __('site.heading.agree_to_terms') ?? 'يجب الموافقة على الشروط والأحكام',
        ]);

        $this->checkoutLoader = true;

        try {
            $customerId = auth()->guard('site')->id();
            if (! $customerId) {
                $this->checkoutLoader = false;
                session()->flash('error', __('site.messages.checkout_login_required'));

                return;
            }

            $data = $this->buildCheckoutData([
                'date' => $this->date,
                'from' => $this->timeFrom,
                'to' => $this->timeTo,
                'payment_method' => $this->paymentMethod,
            ]);
            $providerModel = $this->getProviderModel();
            $cart = (new BuildCartForWebCheckoutAction)->handle($providerModel, $data);
            $providerModel = $this->provider instanceof \App\UsersModule\Models\Provider
                ? $this->provider
                : \App\UsersModule\Models\Provider::find(is_array($this->provider) ? $this->provider['id'] : $this->provider);
            $isFeesOnly = ($providerModel->user?->options?->reservation_flow ?? 'total') === 'fees';
            $total = $isFeesOnly ? ($cart->totals()['reservation_fees_include_taxes'] ?? 0) : $cart->getTotal();

            $dateObj = Carbon::parse($this->date);
            $fromDt = $dateObj->copy()->setTimeFromTimeString($this->timeFrom);
            $toDt = $dateObj->copy()->setTimeFromTimeString($this->timeTo);

            $reservation = $providerModel->reservations()->create([
                'user_id' => $customerId,
                'seat_id' => $this->seat['id'],
                'date' => $fromDt,
                'from' => $fromDt,
                'to' => $toDt,
                'status' => ReservationStatus::PENDING,
                'price' => $cart->getTotal(),
                'meta_data' => [
                    'points' => GeneralSettings::getPointsOnAction('reserve'),
                    'reservation_flow' => $providerModel->user?->options?->reservation_flow ?? 'total',
                ],
            ]);
            $cart->saveItemsToOrder($reservation->id);

            if (! empty($data['wallet'])) {
                $reservation->pay(max((float) $data['wallet'], $total), 'wallet');
            }
            if (! empty($data['points'])) {
                $reservation->pay((float) $data['points'], 'points');
            }

            if ($total > 0) {
                $paymentResponse = $reservation->pay($total, $this->paymentMethod);
                if ($paymentResponse instanceof \Illuminate\Http\RedirectResponse) {
                    $this->checkoutLoader = false;

                    return $paymentResponse;
                }
                if ($paymentResponse instanceof \Illuminate\Http\JsonResponse) {
                    $payload = json_decode($paymentResponse->getContent(), true) ?? [];
                    $url = $payload['redirect_url'] ?? $payload['data']['invoiceURL'] ?? $payload['data']['invoice_url'] ?? null;
                    if (! empty($url)) {
                        $this->checkoutLoader = false;

                        return redirect($url);
                    }
                    if (isset($payload['status']) && ($payload['status'] === 'error' || $payload['status'] === 400)) {
                        try {
                            CancelReservationOnPaymentFailureAction::run($reservation);
                        } catch (\Throwable $e) {
                            //
                        }
                        $this->tabbyError = $payload['message'] ?? __('site.messages.checkout_payment_failed');
                        $this->checkoutLoader = false;

                        return;
                    }
                }
                // MyFatoorah returns Transaction model with meta_data (invoiceURL, etc.)
                if (is_object($paymentResponse) && method_exists($paymentResponse, 'getAttribute')) {
                    $meta = $paymentResponse->meta_data ?? [];
                    if (is_string($meta)) {
                        $meta = json_decode($meta, true) ?? [];
                    }
                    $url = $meta['invoiceURL'] ?? $meta['InvoiceURL'] ?? $meta['invoice_url'] ?? $meta['PaymentURL'] ?? $meta['paymentURL'] ?? null;
                    if (! empty($url)) {
                        $this->checkoutLoader = false;

                        return redirect($url);
                    }
                }

                // Amount due but no redirect URL — cancel pending reservation and surface error
                try {
                    CancelReservationOnPaymentFailureAction::run($reservation);
                } catch (\Throwable $e) {
                    // logged inside action
                }
                $this->checkoutLoader = false;
                $msg = __('site.messages.checkout_payment_redirect_missing');
                $this->tabbyError = $msg;
                session()->flash('error', $msg);

                return;
            }

            if ($isFeesOnly && $total == 0) {
                OrderPaidAction::run($reservation);
            }

            app('cart')->clear();
            session()->forget(['cart_provider_id', 'reservation_data']);
            session()->flash('reservation_id', $reservation->id);
            $this->checkoutLoader = false;

            return redirect()->route('site.booking.completed');
        } catch (\Throwable $e) {
            $this->checkoutLoader = false;
            if (str_contains($e->getMessage(), 'tabby') || str_contains($e->getMessage(), 'disable_tabby')) {
                $this->tabbyError = $e->getMessage();
            } else {
                session()->flash('error', $e->getMessage());
            }
        }
    }

    protected function getProviderModel(): Provider
    {
        return $this->provider instanceof Provider
            ? $this->provider
            : Provider::find(is_array($this->provider) ? $this->provider['id'] : $this->provider);
    }

    protected function buildCheckoutData(array $extra = []): array
    {
        $services = collect($this->cartServices)->map(fn ($s) => [
            'id' => $s['id'],
            'products' => collect($s['products'] ?? [])->map(fn ($p) => ['id' => $p['id'], 'quantity' => $p['quantity'] ?? 1])->toArray(),
        ])->values()->toArray();

        return [
            'seat_id' => $this->seat['id'],
            'services' => $services,
            'coupon_code' => $extra['coupon_code'] ?? $this->couponCode,
            'wallet' => $extra['wallet'] ?? $this->wallet,
            'points' => $extra['points'] ?? $this->points,
            'date' => $extra['date'] ?? $this->date,
            'from' => $extra['from'] ?? $this->timeFrom,
            'to' => $extra['to'] ?? $this->timeTo,
            'payment_method' => $extra['payment_method'] ?? $this->paymentMethod,
        ];
    }

    public function getTotalDurationProperty(): int
    {
        return collect($this->cartServices)->reduce(function ($total, $service) {
            $dur = (int) ($service['duration'] ?? $service['associatedModel']['duration'] ?? 0);
            $products = $service['products'] ?? [];
            $prodDur = collect($products)->sum(fn ($p) => ((int) ($p['duration'] ?? 0)) * ($p['quantity'] ?? 1));

            return $total + $dur + $prodDur;
        }, 0);
    }

    public function render()
    {
        return view('livewire.site.booking-checkout-form');
    }
}
