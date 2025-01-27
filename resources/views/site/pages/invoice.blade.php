<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, shrink-to-fit=no"
    />
    <meta name="description" content="فاتورة الطلب" />
    <title>فاتورة الطلب</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{asset('qr/assets/css/bootstrap.min.css')}}" />
    <!-- Main CSS -->
    <link rel="stylesheet" href="{{asset('qr/assets/css/style.css')}}" />
</head>
<body>
<main id="bodyWrap">
    <div class="container">
        <section class="invoice-header">
            <div class="hold-invoice-header">
                <div class="row align-items-md-center align-items-start">
                    <div class="col-md-2 col-3">
                        <div class="Qcode special_code text-center">
                            <p class="code-tit">فاتورة ضريبية مبسطة</p>
                            <img src="{{asset('qr/assets/img/code.png')}}" alt="Code" class="" />
                            <p class="code-num">45244072</p>
                        </div>
                    </div>
                    <div class="col-md-8 col-6">
                        <div class="logo">
                            <img src="{{asset('qr/assets/img/logo.png')}}" alt="Logo" />
                        </div>
                        <p class="logo-desc text-center">شركة الدعم الذكي</p>
                    </div>
                    <div class="col-md-2 col-3">
                        <div class="Qcode">
                            <img src="{{asset('qr/assets/img/Qcode.png')}}" alt="QR Code" class="" />
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Invoice Details -->
        <div class="invoice">
            <div class="row mb-4">
                <div class="col-12">
                    <div class="row">
                        <div class="col-6">
                            <div class="hold-details">
                                <p><strong>رقم الفاتورة:</strong> 45244072</p>
                                <p>
                                    <strong>تاريخ إصدار الفاتورة:</strong> 2024-12-15 00:59
                                    GMT+03:00
                                </p>
                                <p><strong>حالة الحجز:</strong> جديد</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 text-end">
                    <div class="row">
                        <div class="col-6">
                            <div class="hold-details">
                                <p><strong>اسم العميل:</strong> بدر علي</p>
                                <p><strong>رقم الهاتف:</strong> +966599990445</p>
                                <p><strong>البريد الإلكتروني:</strong></p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="hold-details">
                                <p><strong>طريقة الدفع:</strong> Apple Pay</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Services Table -->
        <section class="tableSection">
            <div class="table-title">الخدمات</div>
            <table>
                <thead>
                <tr>
                    <th>#</th>
                    <th>اسم الخدمة</th>
                    <th>السعر (قبل الضريبة)</th>
                    <th>الضريبة</th>
                    <th>السعر (بعد الضريبة)</th>
                    <th>الإجمالي</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>1</td>
                    <td class="product-name">
                        <p class="product-title">
                            لي ماء وحدائق مطور - 1/2 بوصة - أخضر بخط رمادي - 25 متر
                        </p>
                    </td>
                    <td>SAR 86.96</td>
                    <td>SAR 13.04</td>
                    <td>SAR 100.00</td>
                    <td>SAR 100.00</td>
                </tr>
                <tr>
                    <td>1</td>
                    <td class="product-name">
                        <p class="product-title">
                            لي ماء وحدائق مطور - 1/2 بوصة - أخضر بخط رمادي - 25 متر
                        </p>
                    </td>
                    <td>SAR 86.96</td>
                    <td>SAR 13.04</td>
                    <td>SAR 100.00</td>
                    <td>SAR 100.00</td>
                </tr>
                </tbody>
                <tfoot>
                <tr class="highlight-row">
                    <td colspan="3"></td>
                    <td colspan="2">إجمالي الخدمات</td>
                    <td colspan="1">SAR 100.00</td>
                </tr>
                </tfoot>
            </table>
        </section>

        <!-- Product Table -->
        <section class="tableSection">
            <div class="table-title">المنتجات</div>
            <table>
                <thead>
                <tr>
                    <th>#</th>
                    <th>اسم المنتج</th>
                    <th>السعر (قبل الضريبة)</th>
                    <th>الضريبة</th>
                    <th>السعر (بعد الضريبة)</th>
                    <th>الكمية</th>
                    <th>الإجمالي</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>1</td>
                    <td class="product-name">
                        <p class="product-title">
                            لي ماء وحدائق مطور - 1/2 بوصة - أخضر بخط رمادي - 25 متر
                        </p>
                    </td>

                    <td>SAR 86.96</td>
                    <td>SAR 13.04</td>
                    <td>SAR 100.00</td>
                    <td>1</td>
                    <td>SAR 100.00</td>
                </tr>
                </tbody>
                <tfoot>
                <tr class="highlight-row">
                    <td colspan="4"></td>
                    <td colspan="2">إجمالي المنتجات</td>
                    <td colspan="1">SAR 100.00</td>
                </tr>
                </tfoot>
            </table>
        </section>

        <!-- Pricing Details -->
        <section class="priceDetails">
            <div class="table-title">تفاصيل التكلفة</div>
            <div class="col-lg-7">
                <table class="details-table">
                    <tr>
                        <td>إجمالي تكلفة الخدمات</td>
                        <td>SAR 86.96</td>
                    </tr>
                    <tr>
                        <td>إجمالي تكلفة المنتجات</td>
                        <td>SAR 21.74</td>
                    </tr>
                    <tr>
                        <td>إجمالي التكلفة</td>
                        <td>SAR 108.70</td>
                    </tr>
                    <tr>
                        <td>كود الخصم (ws3)</td>
                        <td>SAR 108.70</td>
                    </tr>
                    <tr>
                        <td>رصيد النقاط</td>
                        <td>SAR 16.30</td>
                    </tr>
                    <tr>
                        <td>رسوم الحجز</td>
                        <td>SAR 125.00</td>
                    </tr>
                    <tr class="total-row">
                        <td>الإجمالي النهائي</td>
                        <td class="final-total">SAR 125.00</td>
                    </tr>
                </table>
            </div>
            <div class="col-5"></div>
        </section>
        <footer>
            <div class="info-row">
            <span
            ><strong>عنوان المتجر:</strong> السعودية الرياض المناخ ينبع</span
            >
                <span><strong>الرقم الضريبي:</strong> 302006492000003</span>
                <span><strong>السجل التجاري:</strong> 1010090173</span>
            </div>
        </footer>
    </div>
</main>
</body>
</html>
