@php($settings = new \App\DefaultPanel\Settings\GeneralSettings())
@php(app()->setLocale('ar'))
        <!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <title>تمونوا - فواتير - {{$order->order_number}}</title>
    <link
            href="https://fonts.googleapis.com/css2?family=Tajawal:wght@500;700&display=swap"
            rel="stylesheet"
    />
    <style>
        * {
            box-sizing: border-box;
        }

        table {
            border-collapse: collapse;
        }

        th,
        td {
            padding: 15px 0;
            line-height: 1.8;
            text-align: start;
            vertical-align: top;
        }

        strong {
            font-weight: 700;
        }

        body {
            font-family: "Tajawal", sans-serif;
            font-size: 18px;
            font-weight: 500;
            color: #000;
            margin: 0;
        }

        img {
            max-width: 100%;
        }

        .content {
            width: 100%;
            max-width: 450px;
            margin: auto;
            background-color: #fff;
            position: relative;
            padding: 40px 15px;
        }

        .head-item {
            margin-bottom: 10px;
        }

        .tables-content {
            width: 100%;
            margin: auto;
        }

        .products {
            width: 100%;
            table-layout: fixed;
            margin-top: 45px;
        }

        .products th,
        .products td {
            border-bottom: 1px dotted #000;
        }

        .products td span {
            display: block;
        }

        .totals {
            width: 100%;
            table-layout: fixed;
            margin-top: 25px;
        }

        .totals td {
            padding: 0 0;
        }

        .totals tr:nth-last-of-type(2) td {
            padding: 0 0 15px;
            font-weight: 700;
            border-bottom: 1px dotted #000;
        }

        .totals tr:last-of-type td {
            padding: 15px 0;
            font-weight: 700;
        }

        .qr {
            padding-top: 30px;
        }

        .qr-title {
            display: block;
            text-align: center;
            font-size: 16px;
            margin-bottom: 7px;
        }

        .qr-img {
            width: 75px;
            margin: 0 auto;
        }

        .logo {
            height: 75px;
            width: auto;
            text-align: center;
            margin-top: 15px;
        }

        .logo img {
            max-height: 100%;
            width: auto;
            max-width: none;
        }

        @media print {
            th,
            td {
                padding: 2mm 0;
                line-height: 1.5;
            }

            body {
                font-size: 2.5mm;
            }

            .content {
                max-width: 55mm;
                padding: 5mm 2mm;
            }

            .head-item {
                margin-bottom: 0.5mm;
            }

            .products {
                margin-top: 6mm;
            }

            .products th,
            .products td {
                border-bottom: 0.3mm dotted #000;
            }

            .totals {
                margin-top: 3mm;
            }

            .totals tr:nth-last-of-type(2) td {
                padding: 0 0 1mm;
                border-bottom: 0.3mm dotted #000;
            }

            .totals tr:last-of-type td {
                padding: 1mm 0;
            }

            .qr {
                padding-top: 3mm;
            }

            .qr-title {
                font-size: 2mm;
                margin-bottom: 0.2mm;
            }

            .qr-img {
                width: 10mm;
                margin: 0 auto;
            }

            .logo {
                height: 10mm;
                margin-top: 1mm;
            }
        }
    </style>
</head>

<body>
<div class="content">
    <div class="head">
        <div class="head-item">
            <strong> رقم الطلب :</strong>
            <span> {{$order->order_number}} </span>
        </div>
        <div class="head-item">
            <strong> تاريخ ووقت الدفع :</strong>
            <span> {{isset($order->payment_data['paid_at']) ? \Carbon\Carbon::parse($order->payment_data['paid_at'])->timezone("africa/cairo")->format('Y-m-d h:i a') : null}} </span>
        </div>
        <div class="head-item">
            <strong> الفرع :</strong>
            <span> {{$order->branch->name}}</span>
        </div>
        <div class="head-item">
            <strong> اسم العميل :</strong>
            <span>{{$order->customer->name}} </span>
        </div>
        <div class="head-item">
            <strong> رقم الجوال :</strong>
            <span> {{$order->customer->phone}} </span>
        </div>
        <div class="head-item">
            <strong> طريقة الدفع :</strong>
            <span> {{$order->payment_data['gateway'] == 'myfatoorah' ?__('panel.enums.credit_card'):__('panel.enums.cash')}} - @lang("panel.enums.".$order->payment_status->value)</span>
        </div>
    </div>
    <div class="tables-content">
        <table class="products">
            <tr>
                <th colspan="2">المنتج</th>
                <th>الكمية</th>
                <th>السعر</th>
                <th>إجمالى</th>
            </tr>
            @foreach($order->as_cart->getContent() as $item )
                <tr>
                    <td colspan="2">
                        {{$item->name}}

                        @foreach($item->attributes['options']??[] as $option)
                                <?php
                                $option_name = \App\DefaultPanel\Lib\Utils::getTranslatedField($option['name']);
                                $value = \App\DefaultPanel\Lib\Utils::getTranslatedField($option['value']);

                                ?>
                            <span>{{$option_name}} :{{$value}} ( +{{$option['price']}})</span>
                        @endforeach
                    </td>
                    <td>{{$item->quantity}}</td>
                    <td>{{$item->getPriceWithConditions()}}</td>
                    <td>{{$item->getPriceSumWithConditions()}}</td>
                </tr>
            @endforeach
        </table>
        @php($totals = $order->as_cart->totals())
        <table class="totals">
            <tr>
                <td colspan="4">اجمالى الطلب</td>
                <td>{{$totals['items_total_with_options']}}</td>
            </tr>
            @if($totals['discount'])
                <tr>
                    <td colspan="4">تخفيض</td>
                    <td>{{$totals['discount']}}</td>
                </tr>
            @endif
            <tr>
                <td colspan="4">ضريبة القيمه المضافة</td>
                <td>{{$totals['taxes']}}</td>
            </tr>
            @if($totals['delivery'])
                <tr>
                    <td colspan="4">قيمة التوصيل</td>
                    <td>{{$totals['delivery']}}</td>
                </tr>
            @endif
            @if($totals['cash_on_delivery_fees'])

                <tr>
                    <td colspan="4">رسوم الدفع عند الاستلام</td>
                    <td>{{$totals['cash_on_delivery_fees']/100}}</td>
                </tr>
            @endif
            <tr>
                <td colspan="4">الاجمالى المستحق</td>
                <td>{{$totals['total']}}</td>
            </tr>

            <tr>
                <td colspan="4">المتبقى</td>
                <td>{{$order->payment_data['gateway'] =='myfatoorah'?0:$totals['total']}}</td>
            </tr>
        </table>
    </div>
    <?php
    $generatedString = \Salla\ZATCA\GenerateQrCode::fromArray([
        new \Salla\ZATCA\Tags\Seller('kufa'),
        new \Salla\ZATCA\Tags\TaxNumber('10000000000'),
        new \Salla\ZATCA\Tags\InvoiceDate($order->created_at?->toIso8601String()),
        new \Salla\ZATCA\Tags\InvoiceTotalAmount($totals['total']),
        new \Salla\ZATCA\Tags\InvoiceTaxAmount($totals['taxes']) // invoice tax amount
    ])->render();
    ?>
    <div class="qr">
        <span class="qr-title"> شكرا لك </span>

        <div class="qr-img">
            <img src="{{$generatedString}}"/>
        </div>
    </div>
    <div class="logo">
        <img src="{{asset("storage/$settings->app_logo")}}"/>
    </div>
</div>
<script>
    window.print();
</script>
</body>
</html>
