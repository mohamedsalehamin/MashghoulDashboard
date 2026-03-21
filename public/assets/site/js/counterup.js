(function () {
    function normalizeDigits(str) {
        // تحويل الأرقام العربية/الفارسية لأرقام إنجليزية
        const map = {
            '٠': '0', '١': '1', '٢': '2', '٣': '3', '٤': '4', '٥': '5', '٦': '6', '٧': '7', '٨': '8', '٩': '9',
            '۰': '0', '۱': '1', '۲': '2', '۳': '3', '۴': '4', '۵': '5', '۶': '6', '۷': '7', '٨': '8', '۹': '9'
        };
        return str.replace(/[٠-٩۰-۹]/g, d => map[d]);
    }

    function splitTextToParts(text) {
        // بنمسك أول رقم (مع كسور/فواصل وعلامات) ونسيب الباقي زي ما هو
        // يدعم: +1,234.56  —  +٣٢١٫٩  —  -42  —  +3.2k
        const re = /[+\-]?[0-9\u0660-\u0669\u06F0-\u06F9][0-9\u0660-\u0669\u06F0-\u06F9.,٬٫]*/;
        const m = text.match(re);
        if (!m) return { prefix: text, numRaw: null, suffix: '', sign: '' };

        const start = m.index;
        const numRaw = m[0];
        const end = start + numRaw.length;

        // استخراج العلامة (+/-)
        const sign = numRaw.startsWith('+') ? '+' : (numRaw.startsWith('-') ? '-' : '');
        const numWithoutSign = sign ? numRaw.slice(1) : numRaw;

        return {
            prefix: text.slice(0, start),
            numRaw: numWithoutSign,
            suffix: text.slice(end),
            sign
        };
    }

    function parseNumeric(numRaw) {
        // ١٢٣٬٤٥٦٫٧٨ -> 123456.78
        let s = normalizeDigits(numRaw);
        s = s.replace(/[,\u066C\s]/g, ''); // حذف فواصل الآلاف: , و ٬
        s = s.replace(/\u066B/g, '.');     // تحويل الفاصل العشري العربي (٫) لنقطة
        const decimals = (s.split('.')[1] || '').length;
        const val = parseFloat(s);
        return { val: isNaN(val) ? 0 : val, decimals };
    }

    function easeOutCubic(t) {
        return 1 - Math.pow(1 - t, 3);
    }

    function animateNumber(elSpan, target, decimals, duration, ease, sign) {
        const start = 0;
        const startTime = performance.now();
        function frame(now) {
            const t = Math.min(1, (now - startTime) / duration);
            const prog = ease(t);
            let value = start + (target - start) * prog;
            // تثبيت الكسور بنفس عدد خانات الهدف
            const text = decimals > 0 ? value.toFixed(decimals) : Math.round(value).toString();
            elSpan.textContent = sign + text;
            if (t < 1) {
                requestAnimationFrame(frame);
            } else {
                // تأكيد القيمة النهائية بالضبط
                const finalText = decimals > 0 ? target.toFixed(decimals) : Math.round(target).toString();
                elSpan.textContent = sign + finalText;
            }
        }
        requestAnimationFrame(frame);
    }

    function prepareElement(el) {
        const raw = el.textContent.trim();
        const { prefix, numRaw, suffix, sign } = splitTextToParts(raw);
        if (!numRaw) return null; // مفيش أرقام؛ مفيش أنيميشن

        // إعادة بناء المحتوى مع تغليف الرقم
        el.innerHTML = '';
        if (prefix) el.append(document.createTextNode(prefix));
        const numSpan = document.createElement('span');
        numSpan.className = 'counter__num';
        numSpan.textContent = sign + '0';
        el.append(numSpan);
        if (suffix) el.append(document.createTextNode(suffix));

        const { val, decimals } = parseNumeric(numRaw);
        return { el, numSpan, target: val, decimals, sign };
    }

    function CounterUp(selector = '.counter', opts = {}) {
        const {
            duration = 1200,
            threshold = 0.4,
            once = true,
            ease = easeOutCubic,
            root = null
        } = opts;

        const items = [];
        document.querySelectorAll(selector).forEach(el => {
            const prepared = prepareElement(el);
            if (prepared) items.push(prepared);
        });

        if (!('IntersectionObserver' in window)) {
            // fallback: شغلها بعد لود الصفحة
            items.forEach(({ el, numSpan, target, decimals, sign }) => {
                el.classList.add('counter--shown');
                animateNumber(numSpan, target, decimals, duration, ease, sign);
            });
            return;
        }

        const io = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const el = entry.target;
                    el.classList.add('counter--shown');
                    const item = items.find(i => i.el === el);
                    if (item) {
                        animateNumber(item.numSpan, item.target, item.decimals, duration, ease, item.sign);
                    }
                    if (once) io.unobserve(el);
                }
            });
        }, { threshold, root });

        items.forEach(({ el }) => io.observe(el));
    }

    // كشفها على الـ window
    window.CounterUp = CounterUp;
})();