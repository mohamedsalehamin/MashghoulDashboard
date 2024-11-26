<?php

namespace App\Exceptions;

use Api;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedOnDomainException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler {
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e) {
        if ($e instanceof TenantCouldNotBeIdentifiedOnDomainException) {
            abort(404);
        }
        if ($e instanceof ModelNotFoundException && $request->wantsJson()) {
            return Api::isNotFound("This record can't be found")->build();
        }
        if ($e instanceof AuthenticationException && $request->wantsJson()) {

            return Api::setStatus(401)->setMessage(__("Unauthenticated"))->build();
        }
        if ($e instanceof NotFoundHttpException && $request->wantsJson()) {
            return Api::isNotFound("Are you lost? ,There is no url matched")->build();
        }
        if ($e instanceof ValidationException && $request->wantsJson()) {
            $messages = $e->validator->errors()->getMessages();


            return Api::setStatusError()
                ->setMessage($e->validator->getMessageBag()->first())
                ->setErrors(collect($messages)->mapWithKeys(fn($errors, $key) => [$key => $errors[0]])->toArray())
                ->build();
        }
        return parent::render($request, $e);
    }
}
