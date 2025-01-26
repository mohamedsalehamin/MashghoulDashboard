<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | such as the size rules. Feel free to tweak each of these messages.
    |
    */
    'current_password' => 'كلمة المرور الحالية غير صحيحة',

    'phone' => 'صيغة رقم الهاتف غير صحيحة',
    'accepted' => 'يجب قبول حقل :attribute',
    'active_url' => 'حقل :attribute لا يُمثّل رابطًا صحيحًا',
    'after' => 'يجب على حقل :attribute أن يكون تاريخًا لاحقًا للتاريخ :date.',
    'after_or_equal' => 'حقل :attribute يجب أن يكون تاريخاً لاحقاً أو مطابقاً للتاريخ :date.',
    'alpha' => 'يجب أن لا يحتوي حقل :attribute سوى على حروف',
    'alpha_dash' => 'يجب أن لا يحتوي حقل :attribute على حروف، أرقام ومطّات.',
    'alpha_num' => 'يجب أن يحتوي :attribute على حروفٍ وأرقامٍ فقط',
    'array' => 'يجب أن يكون حقل :attribute ًمصفوفة',
    'before' => 'يجب على حقل :attribute أن يكون تاريخًا سابقًا للتاريخ :date.',
    'before_or_equal' => 'حقل :attribute يجب أن يكون تاريخا سابقا أو مطابقا للتاريخ :date',
    'between' => [
        'numeric' => 'يجب أن تكون قيمة :attribute بين :min و :max.',
        'file' => 'يجب أن يكون حجم الملف :attribute بين :min و :max كيلوبايت.',
        'string' => 'يجب أن يكون عدد حروف النّص :attribute بين :min و :max',
        'array' => 'يجب أن يحتوي :attribute على عدد من العناصر بين :min و :max',
    ],
    'boolean' => 'يجب أن تكون قيمة حقل :attribute إما true أو false ',
    'confirmed' => 'حقل التأكيد غير مُطابق للحقل :attribute',
    'date' => 'حقل :attribute ليس تاريخًا صحيحًا',
    'date_format' => 'لا يتوافق حقل :attribute مع الشكل :format.',
    'different' => 'يجب أن يكون الحقلان :attribute و :other مُختلفان',
    'digits' => 'يجب أن يحتوي حقل :attribute على :digits رقمًا/أرقام',
    'digits_between' => 'يجب أن يحتوي حقل :attribute بين :min و :max رقمًا/أرقام ',
    "min_digits" => "يجب أن يحتوي حقل :attribute على :min رقم على الأقل",
    'dimensions' => 'الـ :attribute يحتوي على أبعاد صورة غير صالحة.',
    'distinct' => 'للحقل :attribute قيمة مُكرّرة.',
    'email' => 'يجب أن يكون :attribute عنوان بريد إلكتروني صحيح',
    'exists' => 'حقل :attribute غير موجود',
    'file' => 'الـ :attribute يجب أن يكون من ملفا.',
    'filled' => 'حقل :attribute إجباري',
    'image' => 'يجب أن يكون حقل :attribute صورةً',
    'in' => 'حقل :attribute غير صحيح',
    'in_array' => 'حقل :attribute غير موجود في :other.',
    'integer' => 'يجب أن يكون حقل :attribute عددًا صحيحًا',
    'ip' => 'يجب أن يكون حقل :attribute عنوان IP ذا بُنية صحيحة',
    'ipv4' => 'يجب أن يكون حقل :attribute عنوان IPv4 ذا بنية صحيحة.',
    'ipv6' => 'يجب أن يكون حقل :attribute عنوان IPv6 ذا بنية صحيحة.',
    'json' => 'يجب أن يكون حقل :attribute نصا من نوع JSON.',
    'max' => [
        'numeric' => 'يجب أن تكون قيمة حقل :attribute مساوية أو أصغر من :max.',
        'file' => 'يجب أن لا يتجاوز حجم الملف :attribute :max كيلوبايت',
        'string' => 'يجب أن لا يتجاوز طول نص :attribute :max حروفٍ/حرفًا',
        'array' => 'يجب أن لا يحتوي حقل :attribute على أكثر من :max عناصر/عنصر.',
    ],
    'mimes' => 'يجب أن يكون حقل ملفًا من نوع : :values.',
    'mimetypes' => 'يجب أن يكون حقل ملفًا من نوع : :values.',
    'min' => [
        'numeric' => 'يجب أن تكون قيمة حقل :attribute مساوية أو أكبر من :min.',
        'file' => 'يجب أن يكون حجم الملف :attribute على الأقل :min كيلوبايت',
        'string' => 'يجب أن يكون طول نص :attribute على الأقل :min حروفٍ/حرفًا',
        'array' => 'يجب أن يحتوي حقل :attribute على الأقل على :min عُنصرًا/عناصر',
    ],
    'not_in' => 'حقل :attribute غير صحيح',
    'numeric' => 'يجب على حقل :attribute أن يكون رقمًا',
    'present' => 'يجب تقديم حقل :attribute',
    'regex' => 'صيغة حقل :attribute .غير صحيحة',
    'required' => 'حقل :attribute مطلوب.',
    'required_if' => 'حقل :attribute مطلوب في حال ما إذا كان :other يساوي :value.',
    'required_unless' => 'حقل :attribute مطلوب في حال ما لم يكن :other يساوي :values.',
    'required_with' => 'حقل :attribute إذا توفّر :values.',
    'required_with_all' => 'حقل :attribute إذا توفّر :values.',
    'required_without' => 'حقل :attribute إذا لم يتوفّر :values.',
    'required_without_all' => 'حقل :attribute إذا لم يتوفّر :values.',
    'same' => 'يجب أن يتطابق حقل :attribute مع :other',
    'gt' => [
        'numeric' => 'يجب أن تكون قيمة :attribute أكبر من :value.',
        'file' => 'يجب أن يكون حجم الملف :attribute أكبر من :value كيلوبايت',
        'string' => 'يجب أن يكون طول النّص :attribute أكثر من :value حروفٍ/حرفًا',
        'array' => 'يجب أن يحتوي :attribute على أكثر من :value عناصر/عنصر.',
    ],
    'gte' => [
        'numeric' => 'يجب أن تكون قيمة :attribute مساوية أو أكبر من :value.',
        'file' => 'يجب أن يكون حجم الملف :attribute على الأقل :value كيلوبايت',
        'string' => 'يجب أن يكون طول النص :attribute على الأقل :value حروفٍ/حرفًا',
        'array' => 'يجب أن يحتوي :attribute على الأقل على :value عُنصرًا/عناصر',
    ],
    'lt' => [
        'numeric' => 'يجب أن تكون قيمة :attribute أصغر من :value.',
        'file' => 'يجب أن يكون حجم الملف :attribute أصغر من :value كيلوبايت',
        'string' => 'يجب أن يكون طول النّص :attribute أقل من :value حروفٍ/حرفًا',
        'array' => 'يجب أن يحتوي :attribute على أقل من :value عناصر/عنصر.',
    ],
    'lte' => [
        'numeric' => 'يجب أن تكون قيمة :attribute مساوية أو أصغر من :value.',
        'file' => 'يجب أن لا يتجاوز حجم الملف :attribute :max كيلوبايت',
        'string' => 'يجب أن لا يتجاوز طول النّص :attribute :max حروفٍ/حرفًا',
        'array' => 'يجب أن لا يحتوي :attribute على أكثر من :max عناصر/عنصر.',
    ],
    'size' => [
        'numeric' => 'يجب أن تكون قيمة حقل :attribute مساوية لـ :size',
        'file' => 'يجب أن يكون حجم الملف :attribute :size كيلوبايت',
        'string' => 'يجب أن يحتوي النص :attribute على :size حروفٍ/حرفًا بالظبط',
        'array' => 'يجب أن يحتوي حقل :attribute على :size عنصرٍ/عناصر بالظبط',
    ],
    'string' => 'يجب أن يكون حقل :attribute نصآ.',
    'timezone' => 'يجب أن يكون :attribute نطاقًا زمنيًا صحيحًا',
    'unique' => 'قيمة حقل :attribute مُستخدمة من قبل',
    'uploaded' => 'فشل في تحميل الـ :attribute',
    'url' => 'صيغة الرابط :attribute غير صحيحة',
    'at_lest_one_day' => 'يجب اختيار يوم واحد على ألأقل',
    'boundaries_required' => 'يجب رسم الحدود علي الخريطة',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],

        'now' => 'الان'
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [
        'form' => [
            'date' => 'التاريخ',
            'slot' => 'الموعد'
        ],
        'license_number' => 'رقم الرخصة',
        'license_image' => 'صورة الترخيص',
        'tax_number' => 'الرقم الضريبي',
        'lab_name' => 'اسم المختبر',
        'lab_logo' => 'شعار المختبر',
        'full_name' => 'الاسم',
        'name' => 'الاسم',
        'value' => 'القيمة',
        'username' => 'اسم المُستخدم',
        'email' => 'البريد الالكتروني',
        'first_name' => 'الاسم الأول',
        'last_name' => 'الاسم الاخير',
        'password' => 'كلمة المرور',
        'password_confirmation' => 'تأكيد كلمة المرور',
        'city' => 'المدينة',
        'country' => 'الدولة',
        'address' => 'العنوان',
        'phone' => 'الجوال',
        'mobile' => 'الجوال',
        'age' => 'العمر',
        'subject' => 'عنوان الرسالة',
        'sex' => 'الجنس',
        'gender' => 'النوع',
        'day' => 'اليوم',
        'month' => 'الشهر',
        'year' => 'السنة',
        'hour' => 'ساعة',
        'state_id' => 'المنطقة',
        'doctor_name' => 'اسم الطبيب',
        'doctor_image' => 'صورة الطبيب',
        'license' => 'الرخصة',
        'nationality_id' => 'الجنسية',
        'bio' => 'نبذة',
        'job_title' => 'الوظيفة الحالية',
        'minute' => 'دقيقة',
        'second' => 'ثانية',
        'content' => 'المُحتوى',
        'description' => 'الوصف',
        'excerpt' => 'المُلخص',
        'date' => 'التاريخ',
        'time' => 'الوقت',
        'available' => 'مُتاح',
        'size' => 'الحجم',
        'price' => 'السعر',
        'desc' => 'نبذه',
        'title' => 'العنوان',
        'q' => 'البحث',
        'link' => ' ',
        'slug' => ' ',
        'terms' => 'الشروط والأحكام',
        'dob' => 'تاريخ الميلاد',
        'comment' => "سبب الالغاء",
        'city_id' => 'المدينة',
        'device_token' => 'رمز الجهاز',
        "code" => "الكود",
        "zone_id" => "المنطقة",
        'state' => 'الحي',
        'rate' => 'التقييم',
        "old_password" => "كلمة المرور القديمة",
        'coordinate' => 'الموقع',
        'receipt_method' => 'طريقة الاستلام',
        'products' => 'المنتجات',
        'payment_gateway' => 'طريقة الدفع',
        'address_id' => 'العنوان',
        'id' => 'المعرف',
        'status' => 'الحالة',
        'commercial_registration_no' => 'رقم السجل التجاري',
        'commercial_registration_no_image' => 'صورة السجل التجاري',
        'type' => 'النوع',
        'message' => 'الرسالة',
        'contact_type_id' => 'نوع الرسالة',
        'guest' => 'زائر',
        'user_id' => 'المستخدم',
        'branch_id' => 'الفرع',
        'period' => 'الوقت',
        'delivery_method' => 'طريقة التوصيل',
        'institution_activity' => 'نشاط المؤسسة',
        'notes' => 'ملاحظات',
        'current_password' => 'كلمة المرور الحالية',

    ],
    'values' => [
        'due_date' => [
            'now' => 'الان'
        ],
        'start_date' => [
            'today' => 'الان'
        ],
        'end_date' => [
            'today' => 'الان'
        ],
        'before' => [
            'today' => 'الان'
        ]
    ],
    'api' => [
        'coupon_cant_be_used_with_selected_services' => 'كود الخصم لا يمكن استخدامه مع الخدمات المختارة',
        "coupon_code_min_order_value" => 'لاستخدام كود الخصم يجب ان يكون اجمالي الطلب اكبر من :value',
        'insufficient_wallet_balance' => 'رصيد المحفظة غير كافي',
        "overdue_points_balance" => 'النقاط المدفوعة اكبر من قيمة الطلب',
        "insufficient_points_balance"=> 'رصيد النقاط غير كافي',
        "overdue_wallet_balance" => 'الرصيد المدفوع اكبر من قيمة الطلب',
        'product' => [
            'not_exists' => 'المنتج رقم :index غير موجود',
            'not_available' => ":title غير متاح للحجز",
            "time_up" => 'يكون   :title متوفرا من :from الي :to',
            "option_not_exists" => "الخيار رقم :index غير متوفر مع :title",
            'option_unavailable' => "الخيار :option غير متاح حاليا",
            "option_not_available" => " :title غير متوفر حاليا ",
            "value_not_exists" => "قيمه  :title غير صحيحة",
            "value_not_available" => ":title غير متوفر حاليا",
            "option_required" => ":title مطلوب",

        ],
        "coupon_code_not_found" => "كود الخصم غير صحيح",
        "branch_not_available" => "الفرع حاليا غير متاح لاستقبال طلبات جديدة",
        "address_out_side_current_branch" => 'العنوان المختار خارج نطاق توصيل الفرع الحالي',
        "branch_in_maintenance_mode" => 'الفرع حاليا في وضع الصيانة،حاول بعد فترة',
        'invalid_status' => 'الحالة غير صحيحة',
        'invalid_phone_format' => 'صيغة رقم الهاتف غير صحيح',
        "invalid_credentials" => "بيانات الدخول غير صحيحة",
        'invalid_verification_code' => 'كود التفعيل غير صحيح او منتهي الصلاحية',
        "order_not_delivered_yet" => "الطلب لم يتم الانتهاء منه  بعد",
        'reservation_already_rated' => 'تم تقييم الاستشارة مسبقا',
        'reservation_already_reported' => 'تم الابلاغ عن الاستشارة مسبقا',
        'reservation_already_scheduled' => 'تم تحديد موعد لاستشارة مسبقا',
        "invalid_address" => "العنوان غير صحيح",
        'account_suspend' => 'الحساب موقوف حاليا',
        'current_branch_cant_provide_takeaway_method' => 'الفرع الحالي لا يوفر الاستلام من الفرع',
        "coupon_code_is_expired" => 'كود الخصم غير فعال او منتهي الصلاحية',
        "coupon_code_exceeds_the_number_of_usages_times" => 'كود الخصم تجاوز عدد مرات الاستخدام',
        "coupon_code_already_used" => 'كود الخصم مستخدم مسبقا',
        'invalid_date_period' => 'الفترة غير متاحة للحجز',
        'invalid_period' => 'الفترة المختارة غير صحيحة او تم حجزها من طرف عميل اخر',
        'invalid_date' => 'التاريخ المختار خارج مواعيد العمل',


    ],

];
