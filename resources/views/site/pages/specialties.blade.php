@extends('site.layouts.app')
@section("title",__('site.heading.specialties'))
@php($breadcrumb= site()->breadcrumbs()->add(__('site.heading.specialties')))
@section('content')
    <section class="page-content specialties-page">
        <div class="container">
            <h1 class="page-title">@lang('site.heading.specialties')</h1>
            <div class="specialties-grid">
                @foreach($specialties as $specialty)

                    <div class="specialty-item">
                        <a

                            href="{{route('doctors.index',['filters[specialty_id]'=>$specialty->id])}}"


                            class="specialty-img loading-img lazy-img-parent"
                        >
                            <img data-src="{{$specialty->getFirstMediaUrl()}}"
                                 class="lazy-img"
                                 alt="{{$specialty->name}}"
                                 title="{{$specialty->name}}"/>
                        </a>
                        <h3 class="specialty-title">
                            <a> {{$specialty->name}} </a>
                        </h3>
                    </div>
                @endforeach
            </div>
            {{$specialties->links("site.components.pagination")}}
        </div>
    </section>
@endsection
