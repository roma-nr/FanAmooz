/**
 * تنظیمات لوکال جلالی (فارسی) برای flatpickr
 * با استفاده از JDate (کتابخانهٔ jdate.min.js)
 */
(function() {
    if (typeof flatpickr === 'undefined') return;

    // تابع کمکی صفرگذاری
    function pad(n) {
        return n < 10 ? '0' + n : '' + n;
    }

    // تابع ساخت تاریخ میلادی از تاریخ جلالی (برای parseDate)
    function jalaliToGregorian(year, month, day) {
        var jd = new JDate(year, month - 1, day);
        return jd._d;  // Date میلادی
    }

    // تعریف locale
    flatpickr.l10ns.fa = {
        weekdays: {
            shorthand: ['ی', 'د', 'س', 'چ', 'پ', 'ج', 'ش'],
            longhand: ['یکشنبه', 'دوشنبه', 'سه‌شنبه', 'چهارشنبه', 'پنجشنبه', 'جمعه', 'شنبه']
        },
        months: {
            shorthand: ['فرو', 'ارد', 'خر', 'تیر', 'مرد', 'شه', 'مه', 'آبا', 'آذر', 'دی', 'بهم', 'اسف'],
            longhand: ['فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور', 'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند']
        },
        firstDayOfWeek: 6, // شنبه
        ordinal: function() { return ''; },
        formatDate: function(date, format) {
            // date یک Date میلادی است، با JDate به شمسی تبدیل می‌شود
            var jd = new JDate(date);
            return format
                .replace(/Y/g, jd.getFullYear())
                .replace(/m/g, pad(jd.getMonth() + 1))
                .replace(/d/g, pad(jd.getDate()))
                .replace(/H/g, pad(date.getHours()))
                .replace(/i/g, pad(date.getMinutes()))
                .replace(/S/g, pad(date.getSeconds()));
        },
        parseDate: function(dateStr, format) {
            // پشتیبانی از فرمت استاندارد Y/m/d
            var parts = dateStr.split('/');
            if (parts.length === 3) {
                var year = parseInt(parts[0], 10);
                var month = parseInt(parts[1], 10);
                var day = parseInt(parts[2], 10);
                return jalaliToGregorian(year, month, day);
            }
            return null;
        }
    };
})();