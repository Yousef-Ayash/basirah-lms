<template>
    <div
        class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between"
    >
        <div class="flex flex-col gap-2">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">
                {{ title }}
            </h2>
            <span class="text-sm text-gray-500" v-if="info">{{ info }}</span>
        </div>
        <div v-if="$slots.action" class="flex-shrink-0">
            <slot name="action" />
        </div>
    </div>
</template>

<script setup>
defineProps({ title: String, info: String });
</script>
