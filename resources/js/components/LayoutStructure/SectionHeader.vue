<template>
    <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">{{ title }}</h2>
        <div v-if="$slots.action" class="flex-shrink-0">
            <slot name="action" />
        </div>
    </div>
</template>

<script setup>
defineProps({ title: String });
</script>
