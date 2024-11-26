@extends('site.layouts.app')
@section("title",__('site.heading.faqs'))
@php($breadcrumb= site()->breadcrumbs()->add(__('site.heading.faqs')))
@section('content')
    <section class="page-content faq-page">
        <div class="container">
            <h1 class="page-title">@lang('site.heading.faqs')</h1>
            <div class="faq-content">
                <div class="faq-list" id="faq">
                    @foreach($faqs as $index=>$faq)
                        <div class="faq-item">
                            <h3 class="accordion-header faq-header">
                                <button
                                    class="accordion-button faq-button collapsed"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#faq{{$index}}"
                                >
                                    {{$faq->question}}
                                </button>
                            </h3>
                            <div
                                id="faq{{$index}}"
                                class="accordion-collapse faq-collapse collapse"
                                data-bs-parent="#faq"
                            >
                                <div class="accordion-body">
                                    <div class="faq-body">
                                        {{$faq->answer}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>
    </section>
@endsection

