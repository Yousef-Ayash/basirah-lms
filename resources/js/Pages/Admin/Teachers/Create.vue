<script setup>
import AdminLayout from '@/Pages/Admin/Layout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import TeacherForm from '@/components/Teachers/TeacherForm.vue';
import BaseButton from '@/components/FormElements/BaseButton.vue';
import { useTranslations } from '@/composables/useTranslations';

const { __ } = useTranslations();

defineOptions({ layout: AdminLayout });

const form = useForm({
    name: '',
    bio: '',
    photo: null,
});

const submit = () => {
    form.post(route('admin.teachers.store'));
};
</script>

<template>
    <div>
        <Head :title="__('admin.create_teacher')" />
        <h1 class="mb-4 text-2xl font-bold">
            {{ __('admin.create_new_teacher') }}
        </h1>
        <form @submit.prevent="submit">
            <TeacherForm v-model="form" />
            <div class="mt-4 flex justify-end">
                <BaseButton type="submit" :disabled="form.processing">{{
                    __('buttons.create')
                }}</BaseButton>
            </div>
        </form>
    </div>
</template>
