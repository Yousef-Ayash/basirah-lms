<template>
    <div class="flex min-h-screen flex-col bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-white">
        <NavBar :links="navLinks" />

        <main class="mx-auto w-full max-w-7xl flex-grow px-4 py-6">
            <slot />
        </main>
        <Footer />
    </div>
</template>

<script setup>
import Footer from '@/components/LayoutStructure/Footer.vue';
import NavBar from '@/components/LayoutStructure/NavBar.vue';
import { useTranslations } from '@/composables/useTranslations';
import { computed } from 'vue';

const { __ } = useTranslations();

const navLinks = computed(() => [
    { label: __('common.dashboard'), to: route('dashboard') },
    { label: __('common.subjects'), to: route('subjects.index') },
    { label: __('student.my_marks'), to: route('attempts.index') },
    { label: __('student.my_bookmarks'), to: route('bookmarks.index') },
]);
</script>
