<script setup>
import BaseInput from '@/components/FormElements/BaseInput.vue';
import BaseSelect from '@/components/FormElements/BaseSelect.vue';
import BaseTextarea from '@/components/FormElements/BaseTextarea.vue';
import Card from '@/components/LayoutStructure/Card.vue';
import { useTranslations } from '@/composables/useTranslations';

const { __ } = useTranslations();

defineProps({
    modelValue: Object, // The Inertia form object
    users: Array,
    exams: Array,
});

defineEmits(['update:modelValue']);
</script>

<template>
    <Card class="space-y-4">
        <BaseSelect :label="__('common.student')" v-model="modelValue.user_id" :error="modelValue.errors.user_id" required>
            <option disabled :value="null">{{ __('admin.select_student') }}</option>
            <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }} ({{ user.email }})</option>
        </BaseSelect>

        <BaseSelect :label="__('common.exam')" v-model="modelValue.exam_id" :error="modelValue.errors.exam_id" required>
            <option disabled :value="null">{{ __('admin.select_exam') }}</option>
            <option v-for="exam in exams" :key="exam.id" :value="exam.id">{{ exam.title }}</option>
        </BaseSelect>

        <BaseInput
            :label="__('labels.marks_out_of_100')"
            type="number"
            step="0.01"
            v-model="modelValue.marks"
            :error="modelValue.errors.marks"
            required
        />
        <BaseTextarea :label="__('labels.notes_optional')" v-model="modelValue.notes" :error="modelValue.errors.notes" rows="3" />
    </Card>
</template>
