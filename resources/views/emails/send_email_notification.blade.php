<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مشغول</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
            line-height: 1.6;
            text-align: right;
        }
        .email-body  h2,.email-body p{
            text-align: right;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            background-color: #002f87;
            color: #ffffff;
            text-align: center;
            padding: 15px;
            border-radius: 8px 8px 0 0;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
        }
        .email-body {
            padding: 10px;
            text-align: left;
            min-height: 250px;
            background-color: #f8f8f8;
        }
        .email-body h2 {
            color: #002f87;
        }
        .email-footer {
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <h1>مشغول</h1>

        </div>

        <!-- Body -->
        <div class="email-body">
            <h2>{{ $title }}</h2>
            <p>{!! $messageBody  !!}  </p>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <p>أطيب التحيات</p>
            <p>مشغول</p>
        </div>
    </div>
</body>

</html>
