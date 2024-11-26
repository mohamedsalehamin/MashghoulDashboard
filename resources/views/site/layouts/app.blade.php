<!doctype html>
<html lang="{{app()->getLocale()}}" dir="{{site()->direction()}}">

<head>
    @include('site.layouts.app.head')
</head>
<body class="modal-open">
<header>
    @include('site.layouts.app.header')
</header>
@include('site.layouts.app.breadcrumb')

@yield('content')
<footer>
    @include('site.layouts.app.footer')
</footer>
</body>
</html>
