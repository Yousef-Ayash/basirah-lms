<template>
    <div class="bg-white">
        <NavBar :links="navLinks" />

        <main class="mx-auto w-full flex-grow px-4">
            <slot />
        </main>

        <FooterNew />
        <ScrollToTop />
    </div>
</template>

<script setup>
import NavBar from '@/components/New/NavBar.vue';
import { computed } from 'vue';
import FooterNew from '@/components/LayoutStructure/FooterNew.vue';
import ScrollToTop from '@/components/Misc/ScrollToTop.vue';

const navLinks = computed(() => [
    { label: 'الرئيسية', to: route('home') },
    {
        label: 'عن البرنامج',
        dropdown: [
            { label: 'النظام الداخلي', to: route('i-sys') },
            { label: 'الهيئة التدريسية', to: route('teachers') },
            { label: 'المواد الرئيسية', to: route('subjects') },
        ],
    },
    { label: 'عن بصيرة', to: route('about') },
]);
</script>
