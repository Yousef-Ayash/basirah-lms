<script setup>
import AdminLayout from '@/Pages/Admin/Layout.vue';
import BaseButton from '@/components/FormElements/BaseButton.vue';
import MarkForm from '@/components/Marks/MarkForm.vue';
import { useTranslations } from '@/composables/useTranslations';
import { Head, useForm } from '@inertiajs/vue3';

const { __ } = useTranslations();

defineOptions({ layout: AdminLayout });

const props = defineProps({
    users: Array,
    exams: Array,
});

const form = useForm({
    user_id: null,
    exam_id: null,
    marks: '',
    notes: '',
});

const submit = () => {
    form.post(route('admin.marks.store'));
};
</script>

<template>
    <div>
        <Head :title="__('admin.add_mark')" />
        <h1 class="mb-4 text-2xl font-bold">{{ __('admin.add_new_mark') }}</h1>
        <form @submit.prevent="submit">
            <MarkForm v-model="form" :users="users" :exams="exams" />
            <div class="mt-4 flex justify-end">
                <BaseButton type="submit" :disabled="form.processing">{{ __('buttons.save_mark') }}</BaseButton>
            </div>
        </form>
    </div>
</template>
