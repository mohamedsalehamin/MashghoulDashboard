<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no"/>
    <meta name="description" content="التطبيق الأول في السعودية"/>
    <title>مشغول</title>
    <!-- Animate File Css Template -->
    <link rel="stylesheet" href="{{asset('assets/css/animate.min.css')}}"/>
    <!-- owl carousel Css File Template  -->
    <link rel="stylesheet" href="{{asset('assets/css/owl.carousel.min.css')}}"/>
    <!-- FontAwesome Css File Template  -->
    <link rel="stylesheet" href="{{asset('assets/css/all.min.css')}}"/>
    <!-- Bootstrap Css File Template  -->
    <link rel="stylesheet" href="{{asset('assets/css/bootstrap.min.css')}}"/>
    <!-- Main Css File Template -->
    <link rel="stylesheet" href="{{asset('assets/css/style.css')}}"/>
    @livewireStyles
</head>
<body>
<div class="sidebar_pagebody">
    @include('site.components.sidebar')
    <main id="bodyWrap">
        @yield('content')
        @include('site.components.footer')


    </main>

</div>
@livewireScripts


@livewire('livewire-ui-modal')

<!-- jQuery 3.6.4 -->
<script src="{{asset('assets/js/jquery-3.6.4.min.js')}}"></script>

<!-- Popper JS (for Bootstrap tooltips and popovers) -->
<script src="{{asset('assets/js/popper.min.js')}}"></script>

<!-- Bootstrap JS (requires Popper.js) -->
<script src="{{asset('assets/js/bootstrap.bundle.min.js')}}"></script>

<!-- Waypoints (for counter) -->
<script src="{{asset('assets/js/jquery.waypoints.min.js')}}"></script>

<!-- Owl Carousel plugin -->
<script src="{{asset('assets/js/owl.carousel.min.js')}}"></script>

<!-- ScrollrevealMin JS (for animations) -->
<script src="{{asset('assets/js/scrollreveal.min.js')}}"></script>

<!-- WOW.js (for reveal animations) -->
<script src="{{asset('assets/js/wow.min.js')}}"></script>

<!-- Main Plugin JS (optional custom plugin file) -->
<script src="{{asset('assets/js/plugin.js')}}"></script>
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

<!-- Focus plugin -->
<script defer src="https://unpkg.com/@alpinejs/focus@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
