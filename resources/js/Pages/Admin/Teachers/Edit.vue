<script setup>
import AdminLayout from '@/Pages/Admin/Layout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import TeacherForm from '@/components/Teachers/TeacherForm.vue';
import BaseButton from '@/components/FormElements/BaseButton.vue';
import { useTranslations } from '@/composables/useTranslations';

const { __ } = useTranslations();

defineOptions({ layout: AdminLayout });

const props = defineProps({
    teacher: Object,
});

const form = useForm({
    _method: 'PUT',
    name: props.teacher.name,
    bio: props.teacher.bio,
    photo: null,
});

const submit = () => {
    form.post(route('admin.teachers.update', props.teacher.id), {
        forceFormData: true,
    });
};
</script>

<template>
    <div>
        <!-- <Head :title="'Edit Teacher: ' + teacher.name" /> -->
        <Head :title="__('admin.edit_teacher', { teacher: teacher.name })" />
        <h1 class="mb-4 text-2xl font-bold">{{ __('labels.edit_teacher') }}</h1>
        <form @submit.prevent="submit">
            <TeacherForm v-model="form" />
            <div class="mt-4 flex justify-end">
                <BaseButton type="submit" :disabled="form.processing">{{
                    __('buttons.save_changes')
                }}</BaseButton>
            </div>
        </form>
    </div>
</template>
