<template>
    <div>
        <Head :title="__('admin.create_exam')" />
        <h1 class="mb-4 text-2xl font-bold">{{ __('admin.create_new_exam') }}</h1>
        <form @submit.prevent="submit">
            <ExamForm v-model="form" :subjects="props.subjects" />
            <div class="mt-4 flex justify-end">
                <BaseButton type="submit" :disabled="form.processing">{{ __('buttons.create_exam') }}</BaseButton>
            </div>
        </form>
    </div>
</template>

<script setup>
import AdminLayout from '@/Pages/Admin/Layout.vue';
import ExamForm from '@/components/Exams/ExamForm.vue';
import BaseButton from '@/components/FormElements/BaseButton.vue';
import { useTranslations } from '@/composables/useTranslations';
import { Head, useForm, usePage } from '@inertiajs/vue3';

const { __ } = useTranslations();

defineOptions({ layout: AdminLayout });

const props = defineProps({
    subjects: Array,
});

const fullUrl = usePage().url;

// Extract query params
const params = new URLSearchParams(fullUrl.split('?')[1]);
const preselectedSubject = params.get('subject_id');

const form = useForm({
    title: '',
    description: '',
    subject_id: preselectedSubject || null,
    time_limit_minutes: 60,
    max_attempts: 1,
    open_at: '',
    close_at: '',
    review_allowed: true,
    show_answers_after_close: false,
    questions_to_display: 0,
});

const submit = () => {
    form.post(route('admin.exams.store'));
};
</script>
