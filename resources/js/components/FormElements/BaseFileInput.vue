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
            :class="{ 'border-red-500': error, 'border-gray-300': !error }"
        >
            <input
                ref="fileInput"
                type="file"
                name="cover"
                class="absolute inset-0 cursor-pointer opacity-0"
                :accept="accept"
                @change="handleFileChange"
            />
            <span v-if="!selectedFileName"> انقر أو اسحب للرفع </span>
            <span v-else class="font-semibold text-[#61CE70]">{{
                selectedFileName
            }}</span>
        </div>

        <p v-if="error" class="text-sm text-red-500">{{ error }}</p>
        <p v-else-if="hint" class="text-xs text-gray-400 dark:text-gray-500">
            {{ hint }}
        </p>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    modelValue: null, // allow File | null | other
    label: String,
    hint: String,
    accept: String,
    error: String, // Add error prop
});

const emit = defineEmits(['update:modelValue']);
const fileInput = ref(null);

const selectedFileName = computed(() => props.modelValue?.name ?? '');

const handleFileChange = (e) => {
    const file = e.target.files?.[0] ?? null;
    emit('update:modelValue', file);
};

function clear() {
    if (fileInput.value) {
        fileInput.value.value = null;
        emit('update:modelValue', null);
    }
}
</script>
