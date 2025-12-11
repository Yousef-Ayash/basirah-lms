<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    title: String,
    description: String,
    image: String, // Optional: Image URL for social sharing
});

// Get the current URL from Inertia page props (Ziggy/Laravel)
const page = usePage();
const currentUrl = page.props.ziggy?.location || window.location.href;
const appName = 'بصيرة';

// Default fallback image (use absolute URL)
const defaultImage = 'https://basirahonline.com/logo.png';

// Use provided image or fall back to default
const ogImage = computed(() => props.image || defaultImage);
</script>

<template>
    <Head>
        <title>{{ title }}</title>
        <meta name="description" :content="description" />
        <link rel="canonical" :href="currentUrl" />

        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="website" />
        <meta property="og:site_name" :content="appName" />
        <meta property="og:title" :content="title" />
        <meta property="og:description" :content="description" />
        <meta property="og:url" :content="currentUrl" />
        <meta property="og:image" :content="ogImage" />

        <!-- Twitter -->
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="title" />
        <meta name="twitter:description" :content="description" />
        <meta name="twitter:image" :content="ogImage" />
    </Head>
</template>
