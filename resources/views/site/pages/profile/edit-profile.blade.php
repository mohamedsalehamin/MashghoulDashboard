@extends('site.layouts.app')
@section("title",__('site.heading.my_account'))
@php($breadcrumb= site()->breadcrumbs()->add(__('site.heading.my_account')))
@section('content')
    <section class="page-content account-page">
        <div class="container">
            <h1 class="page-title">@lang("site.heading.welcome_customer",['NAME'=>auth()->guard('site')->user()?->name])</h1>
            <div class="row">
                <div class="col-xl-3 col-lg-4 col-12">
                    @livewire('account-side-menu-control')
                </div>
                <div class="col-xl-9 col-lg-8 col-12">
                    <div class="account-body">
                        <h2 class="account-title">@lang("site.heading.edit_account_data")</h2>
                        @livewire('edit-profile-form')
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
