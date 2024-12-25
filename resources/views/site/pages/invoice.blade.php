@php use App\DefaultPanel\Lib\Utils;use Cknow\Money\Money; @endphp
@php(app()->setLocale('ar'))
    <!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <title>تمونوا-فاتورة رقم {{$reservation->id}}</title>
    <base href="/assets/"/>

    <style>
        * {
            box-sizing: border-box;
        }

        table {
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid #a1a1a1;
        }

        th,
        td {
            padding: 13px 10px 9px;
            line-height: 1.5;
        }

        body {
            font-family: Arial;
            font-size: 16px;
            font-weight: 700;
            color: #000;
            margin: 0;
            padding: 26px;
        }

        .content {
            width: 1240px;
            height: 1754px;
            margin: auto;
            background-color: #fff;
            position: relative;
            padding: 44px 60px;
        }

        .logo {
            display: flex;
            justify-content: space-between;
        }

        .logo img {
            width: 180px;
        }

        .title h1 {
            font-size: 28px;
            text-align: center;
        }

        .head {
            width: 703px;
            margin: auto;
            border-top: 2px solid #a1a1a1;
        }

        .head table {
            width: 100%;
            border: none;
        }

        .head table th,
        .head table td {
            width: 185px;
            border: none;
        }

        .tables-content {
            width: 703px;
            margin: auto;
        }

        .products {
            width: 100%;
            table-layout: fixed;
            margin-top: 45px;
        }

        .products th {
            text-align: center;
            background: #e5e6ea;
            padding: 10px 0;
        }

        .products td {
            font-weight: 400;
        }

        .totals {
            width: 100%;
            table-layout: fixed;
            margin-top: 40px;
        }

        .qr {
            display: flex;
            justify-content: center;
            margin-top: 45px;
        }

        .qr img {
            border: 1px solid #a1a1a1;
            width: 176px;
            height: 176px;
        }

        @media print {
            table,
            th,
            td {
                border: 0.2mm solid #a1a1a1;
            }

            th,
            td {
                padding: 2.3mm 2.2mm 2.2mm;
                line-height: 1.5;
            }

            body {
                font-size: 2.71mm;
            }

            .content {
                width: 210mm;
                height: 297mm;
                padding: 7.5mm 10mm;
            }

            .logo img {
                width: 30.5mm;
            }

            .title h1 {
                font-size: 4.74mm;
            }

            .head {
                width: 119mm;
                border-top: 0.4mm solid #a1a1a1;
            }

            .head table th,
            .head table td {
                width: 31.5mm;
            }

            .tables-content {
                width: 119mm;
            }

            .products {
                margin-top: 7.3mm;
            }

            .products th {
                padding: 1.9mm 0;
            }

            .totals {
                margin-top: 6.6mm;
            }

            .qr {
                margin-top: 7mm;
            }

            .qr img {
                border: 0.2mm solid #a1a1a1;
                width: 29.5mm;
                height: 29.5mm;
            }
        }
    </style>
</head>

<body>
<div class="content">
    <div class="logo">
        <img src="{{public_path("assets/img/about-logo.png")}}"/>
    </div>
    <div class="title">
        <h1>
            فاتورة
            <br/>
            Invoice
        </h1>
    </div>
    <div class="head">
        <table>
            <tr>
                <th style="text-align: right">رقم الفاتورة</th>
                <th style="font-weight: 400; text-align: right">{{$reservation->id}}</th>
                <th style="text-align: left; direction: ltr; font-weight: 400">
                    {{$reservation->id}}
                </th>
                <th style="text-align: left; direction: ltr">Invoice Number</th>
            </tr>
            <tr>
                <td style="text-align: right">تاريخ اصدار الفاتورة</td>
                <td style="font-weight: 400; text-align: right">{{now()->format("Y-m-d")}}</td>
                <td style="text-align: left; direction: ltr; font-weight: 400">
                    {{now()->format("Y-m-d")}}
                </td>
                <td style="text-align: left; direction: ltr">Invoice Issue Date</td>
            </tr>
        </table>
    </div>
    <div class="tables-content">
        <table class="products">
            <tr>
                <th>
                    Item Subtotal Include VAT
                    <br/>
                    المجموع (شامل ضريبة القيمة المضافة)
                </th>
                <th>
                    Quantity
                    <br/>
                    الكمية
                </th>
                <th>
                    Unit Price
                    <br/>
                    سعر الوحدة
                </th>
                <th colspan="2">
                    Goods/ Services
                    <br/>
                    سعر الوحدة
                </th>
            </tr>
            @foreach($reservation->itemsLine as $service)
                <tr>
                    <td>{{\Cknow\Money\Money::parse($service->price)->format()}}</td>
                    <td>1</td>
                    <td>{{\Cknow\Money\Money::parse($service->price)->format()}}</td>
                    <td colspan="2" style="text-align: left; direction: ltr">
                        {{$service->name}}
                        <ul>
                            @foreach ($service['attributes']['products']??[] as $index => $option)
                                    <?php
                                    $option_name = Utils::getTranslatedField($option['title']);
                                    $price = Money::parse($option['price']['amount'])->format();
                                    echo "<li>{$option_name} ({$price}) </li>";
                                    ?>
                            @endforeach
                        </ul>
                    </td>
                </tr>
            @endforeach

        </table>
        <table class="totals">
            {{--            <tr>--}}
            {{--                <td>230.00 SAR</td>--}}
            {{--                <td colspan="2" style="border-left: none">--}}
            {{--                    الاجمالي الخاضع للضريبة (غير شامل ضريبة القيمة المضافة)--}}
            {{--                </td>--}}
            {{--                <td--}}
            {{--                    colspan="2"--}}
            {{--                    style="text-align: left; direction: ltr; border-right: none"--}}
            {{--                >--}}
            {{--                    Total Taxable Amount (Excluding VAT)--}}
            {{--                </td>--}}
            {{--            </tr>--}}
            {{--            <tr>--}}
            {{--                <td>230.00 SAR</td>--}}
            {{--                <td colspan="2" style="border-left: none">--}}
            {{--                    مجموع ضريبة القيمة المضافة--}}
            {{--                </td>--}}
            {{--                <td--}}
            {{--                    colspan="2"--}}
            {{--                    style="text-align: left; direction: ltr; border-right: none"--}}
            {{--                >--}}
            {{--                    Total VAT--}}
            {{--                </td>--}}
            {{--            </tr>--}}
            <tr>
                <td>{{$reservation->price}}</td>
                <td colspan="2" style="border-left: none">إجمالي المبلغ المستحق</td>
                <td
                    colspan="2"
                    style="text-align: left; direction: ltr; border-right: none"
                >
                    Total Amount Due
                </td>
            </tr>
        </table>
    </div>
</div>
</body>
</html>
