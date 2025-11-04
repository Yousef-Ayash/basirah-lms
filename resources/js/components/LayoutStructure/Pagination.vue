<template>
    <div v-if="links.length > 3" class="-mb-1 flex flex-wrap">
        <template v-for="(link, key) in links" :key="key">
            <div
                v-if="link.url === null"
                class="mr-1 mb-1 rounded border px-4 py-3 text-sm leading-4 text-gray-400"
                v-html="link.label"
            />
            <Link
                v-else
                class="mr-1 mb-1 rounded border px-4 py-3 text-sm leading-4 hover:bg-white focus:border-indigo-500 focus:text-indigo-500 dark:hover:bg-gray-300 dark:hover:text-gray-700"
                :class="{
                    'bg-gray-300 font-bold text-gray-700 dark:bg-gray-200':
                        link.active,
                }"
                :href="link.url"
                v-html="link.label"
            />
        </template>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    links: Array,
});
</script>
