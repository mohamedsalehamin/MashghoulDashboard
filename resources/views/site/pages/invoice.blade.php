@php use App\DefaultPanel\Lib\Utils;use Cknow\Money\Money; @endphp
@php($settings = new \App\DefaultPanel\Settings\GeneralSettings())
@php($totals = $reservation->print_cart->formattedTotals())
@php($totalsWithoutFormat = $reservation->print_cart->totals())
@php(app()->setLocale('ar'))
    <!DOCTYPE html>
<html lang="en" dir="rtl">
<head>
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, shrink-to-fit=no"
    />
    <meta name="description" content="فاتورة الطلب"/>
    <title>فاتورة الطلب</title>
    <style>

        /* body */
        body {
            font-family: "din-light";
            margin: 0;
        }

        .logo {
        }

        .logo-desc {
            font-size: 18px;
            margin-top: 12px;
        }

        .table-title {
            color: #000;
            font-size: 20px;
            font-family: "din-bold";
            margin-bottom: 12px;
        }

        .info-row {
            justify-content: space-between;
            align-items: center;
            font-size: 14px;
            color: #555;
            border-top: 1px solid #ddd;
            padding: 20px;
            margin-top: 45px;
        }

        .info-row span {
            white-space: nowrap;
            font-size: 16px;
        }

        .invoice-header {
            border-top: 15px solid #000084;
            overflow: hidden;
        }

        .invoice {
            overflow: hidden;
        }

        .tableSection table,
        .details-table {
            border-collapse: collapse;
        }

        table th,
        table td {
            padding: 20px;
            width: auto;
            text-align: center;
        }

        table th {
            background-color: #f2f2f2;
            color: #263b8a;
        }

        table tr {
        }

        .highlight-row td {
            background-color: #f8f8f8;
            font-weight: bold;
        }

        .highlight-row td:nth-of-type(2),
        .highlight-row td:last-child {
            font-size: 18px;
        }

        .details-table td {
            text-align: start;
            padding: 8px 0;
            border-bottom: 1px solid #ddd;
        }

        .details-table .total-row {
            font-weight: bold;
            border-top: 3px solid #000084;
        }

        .details-table .total-row td {
            color: #000084;
            font-family: "din-bold";
            font-size: 18px;
        }

        .details-table .final-total {
            color: #00008b;
            font-size: 18px;
        }

        .hold-invoice-header {
            margin-top: 20px;
        }

        @media (max-width: 992px) {
            .table-title {
                font-size: 18px;
            }

            table th,
            table td {
                font-size: 14px;
                padding: 15px;
            }

            .details-table td {
                font-size: 14px;
                padding: 5px 0;
            }

            .info-row span {
                font-size: 14px;
            }

            .details-table .total-row td {
                font-size: 15px;
            }

            .highlight-row td:nth-of-type(2),
            .highlight-row td:last-child {
                font-size: 14px;
            }
        }

        /* .invoice-header img:not(.logo img) {
          width: 100%;
        } */

        .tableSection {
            margin-bottom: 30px;
        }

        @media (max-width: 768px) {
            .tableSection table,
            .details-table {
                display: block;
                overflow-x: auto;
                overflow-y: hidden;
                -webkit-overflow-scrolling: touch;
            }
        }

        .mb-4 {
            margin-bottom: 1.5rem !important;
        }

        .container,
        .container-fluid,
        .container-lg,
        .container-md,
        .container-sm,
        .container-xl,
        .container-xxl {
            --bs-gutter-x: 1.5rem;
            --bs-gutter-y: 0;
            width: 100%;
            margin-right: auto;
            margin-left: auto;
        }

        @media (min-width: 576px) {
            .container,
            .container-sm {
                max-width: 540px;
            }
        }

        @media (min-width: 768px) {
            .container,
            .container-md,
            .container-sm {
                max-width: 720px;
            }
        }

        @media (min-width: 992px) {
            .container,
            .container-lg,
            .container-md,
            .container-sm {
                max-width: 960px;
            }
        }

        @media (min-width: 1200px) {
            .container,
            .container-lg,
            .container-md,
            .container-sm,
            .container-xl {
                max-width: 1140px;
            }
        }

        @media (min-width: 1400px) {
            .container,
            .container-lg,
            .container-md,
            .container-sm,
            .container-xl,
            .container-xxl {
                max-width: 1320px;
            }
        }

        .row {
            --bs-gutter-x: 1.5rem;
            --bs-gutter-y: 0;
            margin-top: calc(-1 * var(--bs-gutter-y));

            overflow: hidden;
            display: inline;
        }

        .row > * {
            width: 100%;
            max-width: 100%;
            padding-right: calc(-0.5 * var(--bs-gutter-x));
            padding-left: calc(-0.5 * var(--bs-gutter-x));
            margin-top: var(--bs-gutter-y);
        }

        .col-1 {
            float: right;
            width: 8.33333333%;
        }

        .col-2 {
            float: right;
            width: 16.66666667%;
        }

        .col-3 {
            float: right;
            width: 25%;
        }

        .col-4 {
            float: right;
            width: 33.33333333%;
        }

        .col-5 {
            float: right;
            width: 41.66666667%;
        }

        .col-6 {
            float: right;
            width: 50%;
        }

        .col-7 {
            float: right;
            width: 58.33333333%;
        }

        .col-8 {
            float: right;
            width: 66.66666667%;
        }

        .col-9 {
            float: right;
            width: 75%;
        }

        .col-10 {
            float: right;
            width: 83.33333333%;
        }

        .col-11 {
            float: right;
            width: 91.66666667%;
        }

        .col-12 {
            float: right;
            width: 100%;
        }

        .text-start {
            text-align: left !important;
        }

        .text-end {
            text-align: right !important;
        }

        .text-center {
            text-align: center !important;
        }

        .right {
            float: right;
        }

        .left {
            float: left;
        }

        .block {
            display: block;
        }
    </style>
</head>
<body>
<main id="bodyWrap">
    <div class="container">
        <section class="invoice-header">
            <div class="hold-invoice-header">
                <div class="row">
                    <div class="col-md-2 col-3">
                        <div class="Qcode right text-center">
                            <p class="code-tit">فاتورة ضريبية مبسطة</p>
                            <img src="https://barcodeapi.org/api/39/{{$settings->tax_number}}" alt="Code" class=""
                                 width="200"/>
                            {{--                            <p class="code-num">{{$settings->tax_number}}</p>--}}
                        </div>
                    </div>
                    <div class="col-md-8 col-6">
                        <div class="logo text-center">
                            <img src="{{asset('storage/'.$settings->app_logo)}}" alt="Logo"/>
                        </div>
                        <p class="logo-desc text-center">{{$settings->app_name}}</p>
                    </div>
                    <div class="col-md-2 col-3">
                        <div class="Qcode left">
                            <img src="https://quickchart.io/qr?text={{request()->url()}}" alt="QR Code" class=""
                                 width="150"/>
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
                                <p><strong>رقم الفاتورة:</strong> {{$reservation->reservation_number}}</p>
                                <p>
                                    <strong>تاريخ إصدار الفاتورة:</strong>
                                    {{now()->translatedFormat("Y-m-d h:i a")}}
                                </p>
                                <p><strong>حالة الحجز:</strong> {{$reservation->status->getLabel()}}</p>
                            </div>
                        </div>
                        <div class="col-6"></div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="row">
                        <div class="col-6">
                            <div class="hold-details">
                                <p><strong>اسم العميل:</strong>{{$reservation->customer?->name}}</p>
                                <p><strong>رقم
                                        الهاتف:</strong> {{$reservation?->customer?->phone}}</p>
                                <p><strong>البريد الإلكتروني:</strong> {{$reservation?->customer?->email}}</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="hold-details">
                                <p><strong>طريقة الدفع:</strong> {{$reservation->transaction->meta_data['method']??''}}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Services Table -->
        <section class="tableSection">
            <div class="table-title">الخدمات</div>
            <table width="100%">
                <thead>
                <tr>
                    <th>#</th>
                    <th colspan="2">اسم الخدمة</th>
                    <th colspan="2">السعر</th>
                    <th colspan="1">الإجمالي</th>
                </tr>
                </thead>
                <tbody>
                @foreach($reservation->itemsLine as $service)
                    <tr>
                        <td>{{$loop->iteration}}</td>
                        <td colspan="2" class="product-name"><p class="product-title">{{$service->name}}</p></td>
                        <td colspan="2">{{\Cknow\Money\Money::parse($service->price)->format()}}</td>
                        <td colspan="1">{{\Cknow\Money\Money::parse($service->price)->format()}}</td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr class="highlight-row">
                    <td colspan="3"></td>
                    <td colspan="2">إجمالي الخدمات</td>
                    <td colspan="1">{{$totals['services_total']}}</td>
                </tr>
                </tfoot>
            </table>
        </section>

        @if($reservation->as_cart->totals()['products_total'])
            <section class="tableSection">
                <div class="table-title">المنتجات</div>
                <table width="100%">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th colspan="2">اسم المنتج</th>
                        <th>السعر</th>
                        <th colspan="2">الكمية</th>
                        <th>الإجمالي</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($reservation->itemsLine as $service)

                        @foreach ($service['attributes']['products']??[] as $index => $option)
                            <tr>

                                    <?php
                                    $option_name = Utils::getTranslatedField($option['title']);
                                    $price = Money::parse($option['price']['amount'])->format();

                                    ?>
                                <td>{{$loop->iteration}}</td>

                                <td colspan="2" class="product-name">
                                    <p class="product-title">
                                        {{$option_name}}
                                    </p>
                                </td>

                                <td>{{$price}}</td>

                                <td colspan="2">{{$option['price']['quantity']??1}}</td>
                                <td>{{$price}}</td>
                            </tr>
                        @endforeach
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr class="highlight-row">
                        <td colspan="3"></td>
                        <td colspan="3">إجمالي المنتجات</td>
                        <td colspan="1">{{$totals['products_total']}}</td>
                    </tr>
                    </tfoot>
                </table>
            </section>
        @endif

        <!-- Pricing Details -->
        <section class="priceDetails">
            <div class="table-title">تفاصيل التكلفة</div>
            <div class="row block">
                <table width="100%" class="details-table">
                    <tbody>
                    <tr>
                        <td>إجمالي تكلفة الخدمات</td>
                        <td>{{$totals['services_total']}}</td>
                    </tr>
                    <tr>
                        <td>إجمالي تكلفة المنتجات</td>
                        <td>{{$totals['products_total']}}</td>
                    </tr>
                    <tr>
                        <td>
                            رسوم الحجز
                            @if(data_get($reservation->meta_data,'reservation_flow','') =='fees' && $reservation->isPaid())
                                <span class="text-danger"> (تم دفعها)</span>
                            @endif
                        </td>
                        <td>{{$totals['reservation_fees']}}</td>
                    </tr>
                    <tr>
                        <td>إجمالي التكلفة</td>
                        <td>
                            {{$totals['subtotal']}}
                        </td>
                    </tr>
                    <tr>
                        <td>ضريبة القيمة المضافة</td>
                        <td>{{$totals['taxes']}}</td>
                    </tr>
                    <tr>
                        <td>كود الخصم</td>
                        <td>{{$totals['discount']}}</td>
                    </tr>
                    <tr>
                        <td>رصيد النقاط</td>
                        <td>{{$totals['wallet_discount']}}</td>
                    </tr>


                    <tr class="total-row">
                        <td>الإجمالي النهائي</td>
                        <td class="final-total">
                            @if(data_get($reservation->meta_data,'reservation_flow','') =='fees' &&$reservation->isPaid())
                                {{Money::parse($totalsWithoutFormat['total'] - $totalsWithoutFormat['reservation_fees'])->format()}}
                            @else
                                {{$totals['total']}}
                            @endif
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </section>
        <footer>
            <div class="info-row">
                <div class="col-4 text-center">
              <span
              ><strong>عنوان المتجر:</strong>{{$settings->app_address}}</span>
                </div>
                <div class="col-4 text-center">
                    <span><strong>الرقم الضريبي:</strong> {{$settings->tax_number}}</span>
                </div>
                <div class="col-4 text-center">
                    <span><strong>السجل التجاري:</strong> {{$settings->commercial_register}}</span>
                </div>
            </div>
        </footer>
    </div>
</main>
</body>
</html>
