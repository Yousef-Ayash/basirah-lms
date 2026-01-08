<template>
    <Card class="space-y-4">
        <BaseInput
            label="عنوان المقرر"
            v-model="local.title"
            :error="local.errors?.title"
            required
            @input="emitModel"
        />

        <BaseTextarea
            label="الوصف"
            v-model="local.description"
            :error="local.errors?.description"
            rows="4"
            @input="emitModel"
        />

        <div>
            <BaseInput
                label="الترتيب"
                type="number"
                v-model="local.order"
                :error="local.errors?.order"
                placeholder="اتركه فارغاً للترتيب التلقائي"
                @input="emitModel"
            />
            <p class="mt-1 text-xs text-gray-500">
                يحدد ترتيب ظهور المقرر في القوائم.
            </p>
        </div>

        <BaseFileInput
            label="صورة الغلاف (اختياري)"
            :error="local.errors?.cover"
            accept="image/*"
            @update:modelValue="onFileUpdate"
        />

        <div v-if="local.cover && isFile(local.cover)" class="mt-2 text-sm">
            ملف مختار: {{ local.cover.name }} ({{
                formatBytes(local.cover.size)
            }})
        </div>
        <!-- <img
            v-else-if="local.cover_url"
            :src="local.cover_url"
            class="mt-2 max-w-xs rounded-lg border border-gray-200 dark:border-gray-700"
            alt="cover preview"
        /> -->
        <div
            v-else-if="local.cover_url && !local.remove_cover"
            class="group relative mt-2 inline-block"
        >
            <img
                :src="local.cover_url"
                class="max-w-xs rounded-lg border border-gray-200 dark:border-gray-700"
                alt="cover preview"
            />
            <button
                type="button"
                @click="markRemove"
                class="absolute top-2 right-2 rounded-full bg-red-500 p-1 text-white opacity-0 shadow transition-opacity group-hover:opacity-100 hover:bg-red-600"
                title="حذف الصورة"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"
                    />
                </svg>
            </button>
        </div>
        <div v-if="local.remove_cover" class="mt-2 text-sm text-red-500">
            سيتم حذف الصورة الحالية عند الحفظ.
            <button
                type="button"
                @click="undoRemove"
                class="mx-2 text-gray-500 underline hover:text-gray-700"
            >
                تراجع
            </button>
        </div>
    </Card>
</template>

<script setup>
import { reactive, watch, toRaw } from 'vue';
import Card from '@/components/LayoutStructure/Card.vue';
import BaseInput from '@/components/FormElements/BaseInput.vue';
import BaseTextarea from '@/components/FormElements/BaseTextarea.vue';
import BaseFileInput from '@/components/FormElements/BaseFileInput.vue';

const props = defineProps({
    modelValue: { type: Object, required: true },
});

const emit = defineEmits(['update:modelValue']);

// Make a shallow reactive copy of the form object
const local = reactive({ ...props.modelValue });

// Sync local state when parent prop changes
watch(
    () => props.modelValue,
    (newVal) => {
        Object.keys(local).forEach((k) => delete local[k]);
        Object.assign(local, newVal || {});
    },
    { deep: true },
);

function emitModel() {
    emit('update:modelValue', toRaw({ ...local }));
}

function onFileUpdate(fileOrNull) {
    local.cover = fileOrNull;
    emitModel();
}

function isFile(v) {
    return (
        v &&
        typeof v === 'object' &&
        typeof v.name === 'string' &&
        typeof v.size === 'number'
    );
}

function markRemove() {
    local.remove_cover = true;
    emitModel();
}

function undoRemove() {
    local.remove_cover = false;
    emitModel();
}

function formatBytes(bytes, decimals = 1) {
    if (!bytes || bytes === 0) return '0 B';
    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
}

console.log(local.cover_url);
</script>
