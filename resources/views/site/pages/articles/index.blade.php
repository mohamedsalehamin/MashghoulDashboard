@extends('site.layouts.app')
@section("title",__('site.heading.articles'))
@php($breadcrumb= site()->breadcrumbs()->add(__('site.heading.articles')))
@section('content')
    <section class="page-content blog-page">
        @livewire('articles-list')
    </section>
@endsection
