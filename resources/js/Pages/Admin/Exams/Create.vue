<template>
    <div>
        <Head title="إنشاء اختبار جديد" />
        <h1 class="mb-4 text-2xl font-bold">إنشاء اختبار جديد</h1>
        <form @submit.prevent="submit">
            <ExamForm v-model="form" :subjects="props.subjects" />
            <div class="mt-4 flex justify-end">
                <BaseButton type="submit" :disabled="form.processing"
                    >إنشاء اختبار</BaseButton
                >
            </div>
        </form>
    </div>
</template>

<script setup>
import AdminLayout from '@/Pages/Admin/Layout.vue';
import ExamForm from '@/components/Exams/ExamForm.vue';
import BaseButton from '@/components/FormElements/BaseButton.vue';

import { Head, useForm, usePage } from '@inertiajs/vue3';

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
    time_limit_minutes: 45,
    max_attempts: 3,
    distance_between_attempts: 0,
    open_at: '',
    close_at: '',
    review_allowed: false,
    show_answers_after_close: false,
    questions_to_display: 40,
    full_mark: 100,
    pass_threshold: 60,
});

const submit = () => {
    form.post(route('admin.exams.store'));
};
</script>
