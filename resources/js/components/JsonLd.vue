<script setup>
import { Head } from '@inertiajs/vue3';

defineProps({
    data: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <Head>
        <component
            :is="'script'"
            type="application/ld+json"
            v-html="JSON.stringify(data)"
        />
    </Head>
</template>
