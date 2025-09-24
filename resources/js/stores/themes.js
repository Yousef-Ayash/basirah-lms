import { defineStore } from 'pinia';
import { computed, ref, watch } from 'vue';

export const useThemeStore = defineStore('theme', () => {
    // State: Initialize theme from localStorage or default to 'system'
    const theme = ref(localStorage.getItem('theme') || 'system');

    const nextTheme = computed(() => {
        const themes = ['light', 'dark', 'system'];
        const currentIndex = themes.indexOf(theme.value);
        return themes[(currentIndex + 1) % themes.length];
    });

    // Watch for changes in the theme state
    watch(theme, (newTheme) => {
        if (newTheme === 'system') {
            // Remove local storage item to defer to system preference
            localStorage.removeItem('theme');
            applySystemTheme();
        } else {
            // Store the user's explicit choice
            localStorage.setItem('theme', newTheme);
            applyTheme(newTheme);
        }
    });

    // Helper function to apply a specific theme
    function applyTheme(themeValue) {
        if (themeValue === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    }

    // Helper to check and apply the system's preferred theme
    function applySystemTheme() {
        const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        applyTheme(mediaQuery.matches ? 'dark' : 'light');

        // Listen for system theme changes
        mediaQuery.onchange = (e) => {
            if (theme.value === 'system') {
                applyTheme(e.matches ? 'dark' : 'light');
            }
        };
    }

    // Action: Cycle through the available themes
    function toggleTheme() {
        theme.value = nextTheme.value;
    }

    // Action: Initialize the theme when the app loads
    function initTheme() {
        if (theme.value === 'system') {
            applySystemTheme();
        } else {
            applyTheme(theme.value);
        }
    }

    return {
        theme,
        nextTheme,
        toggleTheme,
        initTheme,
    };
});
