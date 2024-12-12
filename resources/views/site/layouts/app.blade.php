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
</head>
<body>
<div class="sidebar_pagebody">
    @include('site.components.sidebar')
    <main id="bodyWrap">
        @yield('content')
        @include('site.components.footer')


    </main>

</div>
<div id="joinServiceModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeModal">&times;</span>
        <form id="joinServiceForm" class="container mt-5" autocomplete="on">
            <div class="row">
                <div class="col-md-4">
                    <label for="firstName" class="form-label">الاسم الأول:</label>
                    <input type="text" id="firstName" name="firstName" class="form-control"
                           autocomplete="given-name"
                           required>
                </div>
                <div class="col-md-4">
                    <label for="lastName" class="form-label">الاسم الأخير:</label>
                    <input type="text" id="lastName" name="lastName" class="form-control" autocomplete="family-name"
                           required>
                </div>
                <div class="col-md-4">
                    <label for="email" class="form-label">البريد الإلكتروني:</label>
                    <input type="email" id="email" name="email" class="form-control" autocomplete="email" required>
                </div>
                <div class="col-md-4">
                    <label for="phone" class="form-label">رقم الجوال:</label>
                    <input type="tel" id="phone" name="phone" class="form-control" autocomplete="tel" required>
                </div>
                <div class="col-md-4">
                    <label for="password" class="form-label">كلمة المرور:</label>
                    <input type="password" id="password" name="password" class="form-control"
                           autocomplete="new-password" required>
                </div>
                <div class="col-md-4">
                    <label for="confirmPassword" class="form-label">تأكيد كلمة المرور:</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" class="form-control"
                           autocomplete="new-password" required>
                </div>
                <div class="col-md-4">
                    <label for="gender" class="form-label">الجنس:</label>
                    <select id="gender" name="gender" class="form-select" autocomplete="sex" required>
                        <option value="male">ذكر</option>
                        <option value="female">أنثى</option>
                    </select>
                </div>
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary w-100">تسجيل</button>
                </div>
            </div>
        </form>

        <div id="successMessage" class="success-message" style="display: none;">
            <img src="assets/img/done.gif" alt="Success" class="check-icon">
            <p>تم التسجيل وتقديم طلبك بنجاح سيتم مراجعة الطلب والتواصل معك من خلال الادارة</p>
        </div>
    </div>
</div>
</body>

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
</html>
