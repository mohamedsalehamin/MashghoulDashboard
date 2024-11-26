@extends('site.layouts.app')
@section("title",__('site.heading.my_reservations')." - #" . $reservation)
@php($breadcrumb= site()->breadcrumbs()
->add(__('site.heading.my_reservations'))
->add("#".$reservation)
)
@section('content')
    <section class="page-content account-page">
        <div class="container">
            <h1 class="page-title">@lang("site.heading.welcome_customer",['NAME'=>auth()->guard('site')->user()?->name])</h1>
            <div class="row">
                <div class="col-xl-3 col-lg-4 col-12">
                    <div class="account-nav">
                        @livewire('account-side-menu-control')
                    </div>
                </div>
                <div class="col-xl-9 col-lg-8 col-12">
                    @livewire('profile-reservations-view',['reservation'=>$reservation])
                </div>
            </div>
        </div>
    </section>
@endsection



