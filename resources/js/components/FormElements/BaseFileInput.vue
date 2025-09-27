<template>
    <div class="space-y-1">
        <label
            v-if="label"
            class="block text-sm font-medium text-gray-700 dark:text-gray-200"
        >
            {{ label }}
        </label>

        <div
            class="relative flex cursor-pointer items-center justify-center rounded-md border border-dashed bg-white px-4 py-6 text-sm text-gray-500 transition hover:border-[#61CE70] dark:bg-gray-800 dark:text-gray-400"
        >
            <input
                type="file"
                class="absolute inset-0 cursor-pointer opacity-0"
                :accept="accept"
                @change="handleFileChange"
            />
            <span>
                {{
                    selectedFileName ||
                    __('placeholders.click_or_drag_to_upload')
                }}
            </span>
        </div>

        <p v-if="hint" class="text-xs text-gray-400 dark:text-gray-500">
            {{ hint }}
        </p>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { useTranslations } from '@/composables/useTranslations';

const { __ } = useTranslations();

const props = defineProps({
    modelValue: File,
    label: String,
    hint: String,
    accept: String, // NEW
});

const emit = defineEmits(['update:modelValue']);

const selectedFileName = computed(() => props.modelValue?.name ?? '');

const handleFileChange = (e) => {
    const file = e.target.files?.[0];
    if (file) {
        emit('update:modelValue', file);
    }
};
</script>
