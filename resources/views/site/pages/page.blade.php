@extends('site.layouts.app')
@section("title",$page->title)
@php($breadcrumb= site()->breadcrumbs()->add($page->title))
@section('content')
    <section class="page-content about-page">
        <div class="container">
            <h1 class="page-title">{{$page->title}}</h1>
            <div class="information-description">
                {!!\Illuminate\Support\Str::markdown($page->description )!!}
            </div>
        </div>
    </section>
@endsection

