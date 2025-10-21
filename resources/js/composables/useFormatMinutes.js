/**
 * تحول عدد الدقائق الكلي إلى سلسلة نصية وصفية (باللغة العربية)
 * وكائن يحتوي على الساعات والدقائق المقابلة.
 *
 * @param {number} totalMinutes العدد الكلي للدقائق المراد تحويله.
 * @returns {{
 * hours: number,
 * minutes: number,
 * formattedString: string
 * }} كائن يحتوي على الساعات والدقائق المتبقية والسلسلة النصية الوصفية.
 */
function formatMinutes(totalMinutes) {
    if (typeof totalMinutes !== 'number' || totalMinutes < 0) {
        return {
            hours: 0,
            minutes: 0,
            formattedString: 'إدخال غير صالح',
        };
    }

    // حساب الساعات والدقائق المتبقية
    const hours = Math.floor(totalMinutes / 60);
    const minutes = totalMinutes % 60;

    // --- تحديد صياغة المفرد والجمع للغة العربية ---

    // تصريف كلمة "ساعة"
    const hourLabel = (h) => {
        if (h === 1) return 'ساعة';
        if (h === 2) return 'ساعتان';
        if (h >= 3 && h <= 10) return 'ساعات';
        return 'ساعة'; // للصفر أو أكثر من 10 (يمكن استخدام 'ساعة' أو 'ساعات' حسب السياق)
    };

    // تصريف كلمة "دقيقة"
    const minuteLabel = (m) => {
        if (m === 1) return 'دقيقة';
        if (m === 2) return 'دقيقتان';
        if (m >= 3 && m <= 10) return 'دقائق';
        return 'دقيقة'; // للصفر أو أكثر من 10 (يمكن استخدام 'دقيقة' أو 'دقائق' حسب السياق)
    };

    let formattedString = '';

    if (hours > 0 && minutes > 0) {
        // مثال: 1 ساعة و 30 دقيقة
        formattedString = `${hours} ${hourLabel(hours)} و ${minutes} ${minuteLabel(minutes)}`;
    } else if (hours > 0) {
        // مثال: 1 ساعة (لـ 60 دقيقة)
        formattedString = `${hours} ${hourLabel(hours)}`;
    } else if (minutes > 0) {
        // مثال: 45 دقيقة
        formattedString = `${minutes} ${minuteLabel(minutes)}`;
    } else {
        // مثال: 0 دقيقة
        formattedString = 'صفر دقائق';
    }

    return {
        hours,
        minutes,
        formattedString,
    };
}

export default formatMinutes;
