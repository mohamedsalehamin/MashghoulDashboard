@extends('site.layouts.app')
@section("title",__('site.heading.home'))
@section('content')

    @include('site.sections.banners')
    @include('site.sections.specialties')
    @include('site.sections.dbanner')
    @includeWhen($doctors->count(),'site.sections.doctors')
    @includeWhen($labs->count(),'site.sections.labs')
@endsection
