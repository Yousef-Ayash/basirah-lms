<template>
    <div class="space-y-1">
        <label v-if="props.label" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ props.label }}
        </label>
        <input
            v-bind="$attrs"
            :type="props.type"
            v-model="model"
            class="w-full rounded-lg border px-3 py-2 focus:ring-2 focus:ring-[#61CE70] focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white"
            @blur="$emit('blur', $event)"
        />
        <p v-if="error" class="text-sm text-red-500">{{ error }}</p>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    modelValue: [String, Number],
    label: String,
    type: { type: String, default: 'text' },
    error: String,
});
const emit = defineEmits(['update:modelValue', 'blur']);

const model = computed({
    get: () => props.modelValue,
    set: (val) => emit('update:modelValue', val),
});
</script>
