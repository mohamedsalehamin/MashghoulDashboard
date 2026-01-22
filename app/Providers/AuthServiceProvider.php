<?php

namespace App\Providers;

use App\CatalogModule\Models\Branch;
use App\CatalogModule\Models\Commission\DoctorCommission;
use App\CatalogModule\Models\Commission\LabCommission;
use App\CatalogModule\Models\Reservation;
use App\ContentModule\Models\JoinRequest;
use App\DoctorPanel\Filament\Resources\Option;
use App\DoctorPanel\Filament\Resources\Product;
use App\ContentModule\Models\Banner;
use App\ContentModule\Models\Category;
use App\ContentModule\Models\City;
use App\ContentModule\Models\Contact;
use App\ContentModule\Models\ContactType;
use App\ContentModule\Models\CustomerReview;
use App\ContentModule\Models\Page;
use App\ContentModule\Models\WholesaleRequest;
use App\CrmModule\Models\AddressBook;
use App\CrmModule\Models\Coupon;
use App\CrmModule\Models\Customer;
use App\Policies\AddressBookPolicy;
use App\Policies\BannerPolicy;
use App\Policies\BranchPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\CityPolicy;
use App\Policies\CommissionPolicy;
use App\Policies\ContactPolicy;
use App\Policies\ContactTypePolicy;
use App\Policies\CouponPolicy;
use App\Policies\CustomerPolicy;
use App\Policies\CustomerReviewPolicy;
use App\Policies\DoctorCommissionPolicy;
use App\Policies\JoinRequestPolicy;
use App\Policies\LabCommissionPolicy;
use App\Policies\OptionPolicy;
use App\Policies\OrderPolicy;
use App\Policies\PagePolicy;
use App\Policies\ProductPolicy;
use App\Policies\ReservationPolicy;
use App\Policies\RolePolicy;
use App\Policies\WholesaleRequestPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Spatie\Permission\Models\Role;
use App\Policies\WithdrawalRequestPolicy;
use App\UsersModule\Models\WithdrawalRequest;
class AuthServiceProvider extends ServiceProvider {
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [

        Category::class => CategoryPolicy::class,
        Page::class => PagePolicy::class,
        Banner::class => BannerPolicy::class,
        Contact::class => ContactPolicy::class,
        Role::class => RolePolicy::class,
        City::class => CityPolicy::class,

        CustomerReview::class => CustomerReviewPolicy::class,
        Reservation::class=>ReservationPolicy::class,
        JoinRequest::class=>JoinRequestPolicy::class,
        WithdrawalRequest::class => WithdrawalRequestPolicy::class,



    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void {
        //
    }
}
