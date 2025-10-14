<template>
    <div class="space-y-1">
        <label
            v-if="label"
            :for="id"
            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
        >
            {{ label }}
        </label>
        <select
            :id="id"
            v-bind="$attrs"
            :value="modelValue"
            @change="$emit('update:modelValue', $event.target.value)"
            class="h-[45px] w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 hover:cursor-pointer focus:ring-2 focus:ring-[#61CE70] dark:border-gray-600 dark:bg-gray-700 dark:text-white"
        >
            <slot />
        </select>
        <p v-if="error" class="text-sm text-red-500">{{ error }}</p>
    </div>
</template>

<script setup>
defineProps({
    modelValue: [String, Number],
    label: String,
    error: String,
    id: String,
});
</script>
