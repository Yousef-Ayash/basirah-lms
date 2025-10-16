<template>
    <Card class="space-y-4">
        <!-- Title -->
        <BaseInput
            label="العنوان"
            v-model="local.title"
            :error="local.errors?.title"
            required
            @input="emitModel"
        />

        <!-- Description -->
        <BaseTextarea
            label="الوصف"
            v-model="local.description"
            :error="local.errors?.description"
            rows="5"
            @input="emitModel"
        />

        <!-- Level -->
        <BaseSelect
            label="المستوى"
            v-model="local.level_id"
            :error="local.errors?.level_id"
            @change="emitModel"
        >
            <option :value="0" disabled>-- بلا مستوى --</option>
            <option v-for="level in levels" :key="level.id" :value="level.id">
                {{ level.name }}
            </option>
        </BaseSelect>

        <!-- File input -->
        <BaseFileInput
            label="صورة الغلاف (اختياري)"
            :error="local.errors?.cover"
            accept="image/*"
            @update:modelValue="onFileUpdate"
        />

        <!-- Optional preview: show either uploaded File name or existing cover_url if present -->
        <div v-if="local.cover && isFile(local.cover)" class="mt-2 text-sm">
            ملف مختار: {{ local.cover.name }} ({{
                formatBytes(local.cover.size)
            }})
        </div>
        <img
            v-else-if="local.cover_url"
            :src="local.cover_url"
            class="mt-2 max-w-xs"
            alt="cover preview"
        />
    </Card>
</template>

<script setup>
import { reactive, watch, toRaw } from 'vue';
import Card from '@/components/LayoutStructure/Card.vue';
import BaseInput from '@/components/FormElements/BaseInput.vue';
import BaseTextarea from '@/components/FormElements/BaseTextarea.vue';
import BaseSelect from '@/components/FormElements/BaseSelect.vue';
import BaseFileInput from '@/components/FormElements/BaseFileInput.vue';

const props = defineProps({
    modelValue: { type: Object, required: true },
    levels: { type: Array, default: () => [] },
});

const emit = defineEmits(['update:modelValue']);

// Make a shallow reactive copy of the form object so we can mutate locally
const local = reactive({ ...props.modelValue });

// Keep local in sync when parent replaces the form object
watch(
    () => props.modelValue,
    (newVal) => {
        // replace keys in local
        Object.keys(local).forEach((k) => delete local[k]);
        Object.assign(local, newVal || {});
    },
    { deep: true },
);

// Emit updated model to parent (clone to plain object to avoid Proxy cycles)
function emitModel() {
    emit('update:modelValue', toRaw({ ...local }));
}

// Handler for file updates coming from BaseFileInput
function onFileUpdate(fileOrNull) {
    // fileOrNull is either a File object or null
    local.cover = fileOrNull;
    emitModel();
}

// Utility: detect File
function isFile(v) {
    return (
        v &&
        typeof v === 'object' &&
        typeof v.name === 'string' &&
        typeof v.size === 'number'
    );
}

// Small helper to show file size human readable (optional)
function formatBytes(bytes, decimals = 1) {
    if (!bytes || bytes === 0) return '0 B';
    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
}
</script>
