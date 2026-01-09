import { reactive } from 'vue';

/**
 * Rule must be a function returning true or an error message string.
 * Example rules:
 * const required = (v) => v ? true : "This field is required";
 * const minLen = (n) => (v) => v && v.length >= n ? true : `Minimum ${n} chars`;
 */

export function required(fieldName = 'Field') {
    return (value) => (value || value === 0 ? true : `مطلوب ${fieldName}`);
}

export function minLength(n, fieldName = 'Field') {
    return (value) =>
        value && value.toString().length >= n
            ? true
            : `يجب أن يتكون ${fieldName} من ${n} أحرف على الأقل`;
}

// export function isEmail(value) {
//     const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
//     return re.test(String(value).toLowerCase())
//         ? true
//         : `أدخل بريدًا إلكترونيًا صالحًا`;
// }

export function isPhoneNumber(value) {
    // The regular expression for a flexible international phone number format
    const re = /^\+?[\d\s\(\)-]{8,15}$/;

    // Clean up the input string: ensure it's a string, and remove potential extra characters
    const stringValue = String(value).trim();

    if (re.test(stringValue)) {
        return true;
    } else {
        return `أدخل رقم هاتف صالحًا`;
    }
}

/**
 * Create validator for form with rule map:
 * { field: [ruleFn, ...] }
 * returns { errors, validateField, validateAll, clearErrors }
 */
export function useValidation(rules = {}) {
    const errors = reactive({});
    // initialize keys
    for (const k of Object.keys(rules)) errors[k] = '';

    function validateField(key, value) {
        const fieldRules = rules[key] ?? [];
        for (const rule of fieldRules) {
            const res = rule(value);
            if (res !== true) {
                errors[key] = res;
                return false;
            }
        }
        errors[key] = '';
        return true;
    }

    function validateAll(values = {}) {
        let ok = true;
        for (const key of Object.keys(rules)) {
            const val = values[key];
            if (!validateField(key, val)) ok = false;
        }
        return ok;
    }

    function clearErrors() {
        for (const k of Object.keys(errors)) errors[k] = '';
    }

    return { errors, validateField, validateAll, clearErrors };
}
