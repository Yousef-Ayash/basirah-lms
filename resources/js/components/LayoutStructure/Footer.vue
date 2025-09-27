<template>
    <footer
        class="bottom-0 mt-6 border-t border-gray-200 bg-gray-100 dark:border-gray-700 dark:bg-gray-900"
    >
        <div
            class="mx-auto flex max-w-7xl flex-col items-center justify-between px-4 py-6 text-sm text-gray-600 md:flex-row dark:text-gray-400"
        >
            <p>
                &copy; {{ new Date().getFullYear() }}
                {{ __('footer.copyright') }}
            </p>

            <div class="mt-3 flex space-x-4 md:mt-0">
                <span class="hover:text-[#61CE70]">{{
                    __('footer.about_basirah')
                }}</span>
                <span class="hover:text-[#61CE70]">{{
                    __('footer.contact_us')
                }}</span>
                <span class="hover:text-[#61CE70]">{{
                    __('footer.help')
                }}</span>
            </div>
        </div>
    </footer>
</template>

<script setup>
import { useTranslations } from '@/composables/useTranslations';

const { __ } = useTranslations();
</script>
