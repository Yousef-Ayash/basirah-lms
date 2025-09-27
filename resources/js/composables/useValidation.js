import { reactive } from 'vue';
import { useTranslations } from './useTranslations';

const { __ } = useTranslations();

/**
 * Rule must be a function returning true or an error message string.
 * Example rules:
 * const required = (v) => v ? true : "This field is required";
 * const minLen = (n) => (v) => v && v.length >= n ? true : `Minimum ${n} chars`;
 */

export function required(fieldName = 'Field') {
    return (value) =>
        value || value === 0
            ? true
            : __('validation.required', { field: fieldName });
}

export function minLength(n, fieldName = 'Field') {
    return (value) =>
        value && value.toString().length >= n
            ? true
            : __('validation.min_length', { field: fieldName, n: n });
}

export function isEmail(value) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(String(value).toLowerCase()) ? true : __('validation.email');
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
