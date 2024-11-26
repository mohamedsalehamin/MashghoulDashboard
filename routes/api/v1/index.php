<?php


use App\DefaultPanel\Api\V1\CartServices;
use App\DefaultPanel\Api\V1\Customer\BranchServices;
use App\DefaultPanel\Api\V1\ProductServices;
use App\Http\Middleware\EnsureThatCustomerActive;

Route::prefix('v1')->group(function () {
    require_once __DIR__ . '/auth.php';
    require_once __DIR__ . '/profile.php';
});
