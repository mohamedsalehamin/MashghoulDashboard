@extends('site.layouts.app')
@section("title",$lab->title)
@php($breadcrumb= site()->breadcrumbs()
->add(__('site.heading.labs'),route('labs.index'))
->add($lab->title)
)
@section('content')
    <section class="page-content single-page">
        @livewire('lab-view',['lab'=>$lab])
    </section>
@endsection
