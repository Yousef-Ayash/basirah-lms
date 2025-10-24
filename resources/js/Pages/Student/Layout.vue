<template>
    <div
        class="flex min-h-screen flex-col bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-white"
    >
        <NavBar :links="navLinks" />

        <main class="mx-auto mb-28 w-full max-w-7xl flex-grow px-4 py-6">
            <slot />
        </main>

        <Footer />

        <ScrollToTop />
    </div>
</template>

<script setup>
import Footer from '@/components/LayoutStructure/FooterNew.vue';
import NavBar from '@/components/LayoutStructure/NavBar.vue';
import { computed } from 'vue';
import ScrollToTop from '@/components/Misc/ScrollToTop.vue';

const navLinks = computed(() => [
    { label: 'الرئيسية', to: route('home') },
    { label: 'لوحة التحكم', to: route('dashboard') },
    { label: 'المواد الدراسية', to: route('subjects.index') },
    { label: 'درجاتي', to: route('attempts.index') },
    { label: 'إشاراتي المرجعية', to: route('bookmarks.index') },
]);
</script>
