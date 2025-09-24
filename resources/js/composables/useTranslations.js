import { usePage } from '@inertiajs/vue3';
import { get } from 'lodash';

/**
 * A composable that provides a translation function `__`.
 * This is the recommended way to use translations within the `<script setup>` block.
 */
export function useTranslations() {
    /**
     * Translate the given key from the shared JSON translation file.
     * This function is designed to be reactive and can be called from anywhere
     * within a Vue component's setup or template.
     *
     * @param {string} key The key of the translation string (e.g., 'common.dashboard').
     * @param {object} replacements The placeholder replacements (e.g., { page: 'Home' }).
     */
    const __ = (key, replacements = {}) => {
        // **THE FIX**: Call `usePage()` here, inside the function.
        // This ensures we always get the LATEST props for the current page.
        const { props } = usePage();

        // Safely access the translation from the props.
        let translation = get(props.translations, key, key);

        // Replace placeholders like :name with their values.
        Object.keys(replacements).forEach((r) => {
            if (typeof translation === 'string') {
                translation = translation.replace(`:${r}`, replacements[r]);
            }
        });

        return translation;
    };

    return { __ };
}
