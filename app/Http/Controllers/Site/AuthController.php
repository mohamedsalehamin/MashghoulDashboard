<?php

namespace App\Http\Controllers\Site;

use App\CatalogModule\Models\Plan;
use App\CatalogModule\Models\PlanPrice;
use App\ContentModule\Models\Page;
use App\DefaultPanel\Settings\GeneralSettings;
use App\DefaultPanel\Settings\LandingSettings;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    protected function sharedData(): array
    {
        $settings = new GeneralSettings();
        $landingSettings = new LandingSettings();
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

    public function showLogin()
    {
        // Temporary diagnostics for "login page redirects to home after logout".
        // Remove once the root-cause is confirmed.
        // Also: ensure no stale "location modal" / intended redirect can bounce the user away.
        session()->forget([
            'show_location_modal',
            'intended_url',
            'url.intended',
        ]);

        Log::info('site.login hit', [
            'url' => request()->fullUrl(),
            'path' => request()->path(),
            'locale' => app()->getLocale(),
            'auth_site' => Auth::guard('site')->check(),
            'auth_web' => Auth::guard('web')->check(),
            'session_id' => request()->session()->getId(),
            'session_location_set' => session('location_set'),
            'session_show_location_modal' => session('show_location_modal'),
            'session_url_intended' => session('url.intended'),
            'session_intended_url' => session('intended_url'),
        ]);

        return view('site.new.login', $this->sharedData());
    }

    public function showRegister()
    {
        return view('site.new.register', $this->sharedData());
    }

    /**
     * Provider self-registration (salon account). Public site: /{locale}/join/register.
     *
     * Plan selection can be applied via query string (GET) so navigation works even if
     * Livewire's wire:click fails in the browser. Example: ?plan=1&plan_price=2&payment=myfatoorah
     */
    public function showJoinRegister(Request $request): \Illuminate\Contracts\View\View|RedirectResponse
    {
        if ($request->filled('plan') && $request->filled('plan_price')) {
            $redirect = $this->storeJoinPlanSelectionFromQuery($request);
            if ($redirect instanceof RedirectResponse) {
                return $redirect;
            }

            // Clean URL after persisting session (avoid stale query params on refresh)
            return redirect()->route('site.join.register');
        }

        return view('site.new.join-register', $this->sharedData());
    }

    /**
     * Validate plan/price and persist the join funnel in session (same rules as JoinPlanSelection).
     */
    protected function storeJoinPlanSelectionFromQuery(Request $request): ?RedirectResponse
    {
        $planId = (int) $request->query('plan');
        $planPriceId = (int) $request->query('plan_price');
        $payment = $request->query('payment', 'myfatoorah');

        if (! in_array($payment, ['myfatoorah', 'tabby'], true)) {
            $payment = 'myfatoorah';
        }

        $plan = Plan::query()->enabled()->with('planPrices')->find($planId);
        $planPrice = PlanPrice::query()->find($planPriceId);

        if (! $plan || ! $planPrice || (int) $planPrice->plan_id !== (int) $plan->id) {
            session()->flash('error', __('site.join.invalid_plan_selection'));

            return redirect()->route('site.join');
        }

        session([
            'join_plan_id' => $plan->id,
            'join_plan_price_id' => $planPrice->id,
            'join_payment_method' => $payment,
        ]);

        return null;
    }

    public function showRegisterSuccess()
    {
        return view('site.new.register-success', $this->sharedData());
    }
}
