@extends('site.layouts.app')
@section("title",$doctor->name)
@php($breadcrumb= site()->breadcrumbs()->add(__('site.heading.doctors'),route('doctors.index'))->add($doctor->name))
@section('content')
    <section class="page-content single-page">
        @livewire('doctor-view',['doctor'=>$doctor])
    </section>
@endsection
