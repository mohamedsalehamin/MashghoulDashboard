<meta charset="UTF-8"/>
<meta
    name="viewport"
    content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no"
/>
<title>@lang("site.app_name") - @yield("title")</title>

<link rel="shortcut icon" type="img/png" href="images/favicon.png"/>
<base href="/assets/"/>
@if(site()->direction() =='rtl')
    <link rel="stylesheet" href="css/bootstrap.rtl.min.css"/>
@else
    <link rel="stylesheet" href="css/bootstrap.min.css" />
@endif
<link rel="stylesheet" href="css/fontawesome.min.css"/>
<link rel="stylesheet" href="css/swiper-bundle.min.css"/>
<link rel="stylesheet" href="css/select2.min.css"/>
<link rel="stylesheet" href="css/fancybox.css"/>
<link rel="stylesheet" href="css/intlTelInput.min.css"/>
<link rel="stylesheet" href="css/flatpicker.min.css"/>
<link rel="stylesheet" href="css/main.css"/>

@stack('css')

