<?php

use App\Http\Middleware\AlignPublicDiskUrlWithRequestHost;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\EnsureLocationSet;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\ValidateSignature;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;
use Illuminate\Auth\Middleware\RequirePassword;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;
use Illuminate\Http\Middleware\SetCacheHeaders;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRoutes;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationViewPath;
use Mcamara\LaravelLocalization\Middleware\LocaleCookieRedirect;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/webhooks.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Trust proxies configuration
        $middleware->trustProxies(at: '*');

        // Configure guest redirect (for unauthenticated users)
        $middleware->redirectGuestsTo(fn (Request $request) => $request->expectsJson() ? null : route('auth.login')
        );

        // Configure authenticated user redirect
        $middleware->redirectUsersTo(\App\Providers\RouteServiceProvider::HOME);

        // Use custom middleware classes
        $middleware->encryptCookies(except: []);
        $middleware->validateCsrfTokens(except: []);
        $middleware->trimStrings(except: [
            'current_password',
            'password',
            'password_confirmation',
        ]);

        $middleware->prependToGroup('web', [
            AlignPublicDiskUrlWithRequestHost::class,
        ]);

        // API middleware - append custom middleware
        $middleware->api(append: [
            SetLocale::class,
        ]);

        // Custom middleware groups
        $middleware->group('localization', [
            'web',
            LaravelLocalizationRoutes::class,
            LaravelLocalizationRedirectFilter::class,
            LocaleSessionRedirect::class,
            LaravelLocalizationViewPath::class,
        ]);

        // Middleware aliases
        $middleware->alias([
            'auth' => Authenticate::class,
            'auth.basic' => AuthenticateWithBasicAuth::class,
            'auth.session' => AuthenticateSession::class,
            'cache.headers' => SetCacheHeaders::class,
            'can' => Authorize::class,
            'guest' => RedirectIfAuthenticated::class,
            'password.confirm' => RequirePassword::class,
            'precognitive' => HandlePrecognitiveRequests::class,
            'signed' => ValidateSignature::class,
            'throttle' => ThrottleRequests::class,
            'verified' => EnsureEmailIsVerified::class,
            'localize' => LaravelLocalizationRoutes::class,
            'localizationRedirect' => LaravelLocalizationRedirectFilter::class,
            'localeSessionRedirect' => LocaleSessionRedirect::class,
            'localeCookieRedirect' => LocaleCookieRedirect::class,
            'localeViewPath' => LaravelLocalizationViewPath::class,
            'ensureLocationSet' => EnsureLocationSet::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (ModelNotFoundException $e, Request $request) {
            if ($request->wantsJson()) {
                return \Api::isNotFound("This record can't be found")->build();
            }
        });

        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->wantsJson()) {
                return \Api::setStatus(401)->setMessage(__('Unauthenticated'))->build();
            }
        });

        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->wantsJson()) {
                return \Api::isNotFound('Are you lost? ,There is no url matched')->build();
            }
        });

        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->wantsJson()) {
                $messages = $e->validator->errors()->getMessages();

                return \Api::setStatusError()
                    ->setMessage($e->validator->getMessageBag()->first())
                    ->setErrors(collect($messages)->mapWithKeys(fn($errors, $key) => [$key => $errors[0]])->toArray())
                    ->build();
            }
        });

        // Don't flash sensitive data on validation exceptions
        $exceptions->dontFlash([
            'current_password',
            'password',
            'password_confirmation',
        ]);
    })
    ->create();
