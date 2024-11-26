<!DOCTYPE html>
<html lang="{{app()->getLocale()}}" dir="{{site()->direction()}}">
<head>
    @include("site.layouts.guest.head")
</head>

<body>
    @yield('content')
@include('site.layouts.guest.footer')
</body>
</html>
