<script setup>
import AdminLayout from '@/Pages/Admin/Layout.vue';
import BaseButton from '@/components/FormElements/BaseButton.vue';
import BaseFileInput from '@/components/FormElements/BaseFileInput.vue';
import Card from '@/components/LayoutStructure/Card.vue';
import SectionHeader from '@/components/LayoutStructure/SectionHeader.vue';
import { useTranslations } from '@/composables/useTranslations';
import { Head, useForm } from '@inertiajs/vue3';

const { __ } = useTranslations();

defineOptions({ layout: AdminLayout });

const form = useForm({
    file: null,
});

const submit = () => {
    form.post(route('admin.students.import.preview'));
};
</script>

<template>
    <div>
        <Head :title="__('admin.import_students')" />
        <SectionHeader :title="__('admin.import_students')" />

        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            <div class="md:col-span-1">
                <Card>
                    <h3 class="mb-2 font-semibold">{{ __('admin.import_students_instructions_title') }}</h3>
                    <p class="mb-2 text-sm text-gray-600 dark:text-gray-400">{{ __('admin.import_students_instructions_intro') }}</p>
                    <ul class="list-inside list-disc space-y-1 text-sm">
                        <li>{{ __('admin.import_students_col_name') }}</li>
                        <li>{{ __('admin.import_students_col_email') }}</li>
                        <li>{{ __('admin.import_students_col_level') }}</li>
                        <li>{{ __('admin.import_students_col_approved') }}</li>
                        <li>{{ __('admin.import_students_col_password') }}</li>
                    </ul>
                </Card>
            </div>

            <div class="md:col-span-2">
                <form @submit.prevent="submit">
                    <Card class="space-y-4">
                        <BaseFileInput
                            :label="__('labels.student_data_file')"
                            :hint="__('labels.student_data_file_hint')"
                            v-model="form.file"
                            :error="form.errors.file"
                            accept=".xlsx,.xls,.csv"
                            required
                        />
                        <div class="flex justify-end">
                            <BaseButton type="submit" :disabled="form.processing"> {{ __('buttons.upload_and_preview') }} </BaseButton>
                        </div>
                    </Card>
                </form>
            </div>
        </div>
    </div>
</template>
