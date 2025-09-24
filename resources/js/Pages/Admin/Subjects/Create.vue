<template>
    <div>
        <Head :title="__('admin.create_subject')" />
        <h1 class="mb-4 text-2xl font-bold">{{ __('admin.create_subject') }}</h1>

        <form @submit.prevent="submit">
            <SubjectForm v-model="form" :levels="props.levels" />

            <div class="mt-4 flex justify-between">
                <div class="justify-start">
                    <BaseButton as="a" :href="route('admin.subjects.index')" class="bg-red-500 text-white hover:bg-red-600">
                        {{ __('common.cancel') }}
                    </BaseButton>
                </div>
                <div class="justify-end">
                    <BaseButton type="submit" :disabled="form.processing"> {{ __('buttons.create') }} </BaseButton>
                </div>
            </div>
        </form>
    </div>
</template>

<script setup>
import AdminLayout from '@/Pages/Admin/Layout.vue';
import BaseButton from '@/components/FormElements/BaseButton.vue';
import SubjectForm from '@/components/Subjects/SubjectForm.vue';
import { useTranslations } from '@/composables/useTranslations';
import { Head, useForm } from '@inertiajs/vue3';

const { __ } = useTranslations();

defineOptions({ layout: AdminLayout });

const props = defineProps({
    levels: Array,
});

const form = useForm({
    title: '',
    description: '',
    level_id: 0,
    cover: null,
});

const submit = () => {
    form.post(route('admin.subjects.store'));
};
</script>
