<template>
    <div
        class="flex min-h-screen flex-col bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-white"
    >
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

import { computed } from 'vue';

const navLinks = computed(() => [
    { label: 'لوحة التحكم', to: route('dashboard') },
    { label: 'المواد الدراسية', to: route('subjects.index') },
    { label: 'المدرسون', to: route('teachers.index') },
    { label: 'درجاتي', to: route('attempts.index') },
    { label: 'إشاراتي المرجعية', to: route('bookmarks.index') },
]);
</script>
