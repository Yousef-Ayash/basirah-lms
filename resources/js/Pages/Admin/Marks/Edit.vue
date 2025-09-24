<script setup>
import AdminLayout from '@/Pages/Admin/Layout.vue';
import BaseButton from '@/components/FormElements/BaseButton.vue';
import MarkForm from '@/components/Marks/MarkForm.vue';
import { useTranslations } from '@/composables/useTranslations';
import { Head, useForm } from '@inertiajs/vue3';

const { __ } = useTranslations();

defineOptions({ layout: AdminLayout });

const props = defineProps({
    mark: Object,
    users: Array,
    exams: Array,
});

const form = useForm({
    user_id: props.mark.user_id,
    exam_id: props.mark.exam_id,
    marks: props.mark.marks,
    notes: props.mark.notes,
});

const submit = () => {
    form.put(route('admin.marks.update', props.mark.id));
};
</script>

<template>
    <div>
        <Head :title="__('admin.edit_mark', { name: mark.user.name })" />
        <h1 class="mb-4 text-2xl font-bold">{{ __('admin.edit_mark_page_title') }}</h1>
        <form @submit.prevent="submit">
            <MarkForm v-model="form" :users="users" :exams="exams" />
            <div class="mt-4 flex justify-end">
                <BaseButton type="submit" :disabled="form.processing">{{ __('buttons.update_mark') }}</BaseButton>
            </div>
        </form>
    </div>
</template>
