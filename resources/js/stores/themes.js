import { defineStore } from 'pinia';
import { computed, ref, watch } from 'vue';

export const useThemeStore = defineStore('theme', () => {
    let saved = null;
    try {
        saved = localStorage.getItem('theme');
    } catch (e) {
        // localStorage might be unavailable (SSR or privacy mode) — fall back to detection
        saved = null;
    }

    const prefersDark =
        typeof window !== 'undefined' &&
        window.matchMedia &&
        window.matchMedia('(prefers-color-scheme: dark)').matches;

    // If there is a saved theme use it, otherwise use system preference once
    const initial = saved || (prefersDark ? 'dark' : 'light');

    // State: Initialize theme from localStorage or default to 'system'
    const theme = ref(initial);

    const nextTheme = computed(() =>
        theme.value === 'light' ? 'dark' : 'light',
    );

    // Watch for changes in the theme state
    watch(
        theme,
        (newTheme) => {
            try {
                localStorage.setItem('theme', newTheme);
            } catch (e) {
                // ignore if localStorage is not available
            }
            applyTheme(newTheme);
        },
        { immediate: true },
    );

    // Helper function to apply a specific theme
    function applyTheme(themeValue) {
        if (typeof document === 'undefined') return;
        if (themeValue === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    }

    // Action: Cycle through the available themes
    function toggleTheme() {
        theme.value = nextTheme.value;
    }

    // Action: Initialize the theme when the app loads
    function initTheme() {
        applyTheme(theme.value);
    }

    return {
        theme,
        nextTheme,
        toggleTheme,
        initTheme,
    };
});
