<template>
    <div>
        <Head :title="__('admin.edit_exam_title', { title: exam.title })" />
        <h1 class="mb-4 text-2xl font-bold">{{ __('admin.edit_exam') }}</h1>
        <form @submit.prevent="submit">
            <ExamForm v-model="form" :subjects="subjects" />
            <div class="mt-4 flex justify-end">
                <BaseButton type="submit" :disabled="form.processing">{{
                    __('buttons.save_changes')
                }}</BaseButton>
            </div>
        </form>
    </div>
</template>

<script setup>
import AdminLayout from '@/Pages/Admin/Layout.vue';
import ExamForm from '@/components/Exams/ExamForm.vue';
import BaseButton from '@/components/FormElements/BaseButton.vue';
import { useTranslations } from '@/composables/useTranslations';
import { Head, useForm } from '@inertiajs/vue3';

const { __ } = useTranslations();

defineOptions({ layout: AdminLayout });

const props = defineProps({
    exam: Object,
    subjects: Array,
});

const form = useForm({
    title: props.exam.title,
    description: props.exam.description,
    subject_id: props.exam.subject_id,
    time_limit_minutes: props.exam.time_limit_minutes || 60,
    max_attempts: props.exam.max_attempts || 1,
    distance_between_attempts: props.exam.distance_between_attempts || 0,
    open_at: props.exam.open_at,
    close_at: props.exam.close_at,
    review_allowed: props.exam.review_allowed,
    show_answers_after_close: props.exam.show_answers_after_close,
    questions_to_display: props.exam.questions_to_display || 0,
    questions_to_display: props.exam.full_mark || 100,
});

const submit = () => {
    form.put(route('admin.exams.update', props.exam.id));
};
</script>
