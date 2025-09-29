<script setup>
import BaseInput from '@/components/FormElements/BaseInput.vue';
import BaseTextarea from '@/components/FormElements/BaseTextarea.vue';
import BaseFileInput from '@/components/FormElements/BaseFileInput.vue';
import Card from '@/components/LayoutStructure/Card.vue';
import { useTranslations } from '@/composables/useTranslations';

const { __ } = useTranslations();

defineProps({
    modelValue: Object,
});
</script>

<template>
    <Card class="space-y-4">
        <BaseInput
            :label="__('common.name')"
            v-model="modelValue.name"
            :error="modelValue.errors.name"
            required
        />
        <BaseTextarea
            :label="__('common.bio')"
            v-model="modelValue.bio"
            :error="modelValue.errors.bio"
            rows="5"
        />
        <BaseFileInput
            :label="__('common.photo')"
            @update:modelValue="modelValue.photo = $event"
            :error="modelValue.errors.photo"
            accept="image/*"
        />
    </Card>
</template>
