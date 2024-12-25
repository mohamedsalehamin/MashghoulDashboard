@extends('site.layouts.app')
@section("title",$page->title)
@section('content')
    <header class="head-inside">
        @include('site.components.navbar')
    </header>
    <div class="breadcrumb-container py-3">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-2" style="direction: rtl; text-align: right;">
                    <li class="breadcrumb-item">
                        <a href="{{route('site.home')}}" class="text-decoration-none text-primary">
                            الرئيسية
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{$page->title}}
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="custom-content-section">
        <div class="container">
            <h2 class="page-tit mb-4">{{$page->title}}</h2>
            <div class="row">
                <div class="col-12">
                    <div class="singlePage-content">
                        {!!\Illuminate\Support\Str::markdown($page->description )!!}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

