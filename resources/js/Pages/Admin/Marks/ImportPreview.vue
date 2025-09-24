<script setup>
import AdminLayout from '@/Pages/Admin/Layout.vue';
import BaseButton from '@/components/FormElements/BaseButton.vue';
import Card from '@/components/LayoutStructure/Card.vue';
import SectionHeader from '@/components/LayoutStructure/SectionHeader.vue';
import Alert from '@/components/Misc/Alert.vue';
import { useTranslations } from '@/composables/useTranslations';
import { Head, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const { __ } = useTranslations();

defineOptions({ layout: AdminLayout });

const props = defineProps({
    preview: Array,
    preview_id: String,
});

const form = useForm({
    preview_id: props.preview_id,
});

const hasErrors = computed(() => props.preview.some((row) => row.errors.length > 0));

const submit = () => {
    form.post(route('admin.marks.import.commit'));
};
</script>

<template>
    <div>
        <Head :title="__('admin.confirm_marks_import')" />
        <SectionHeader :title="__('admin.confirm_marks_import')" />
        <Alert v-if="hasErrors" type="error" :message="__('messages.file_contains_errors')" class="mb-4" />
        <Alert v-else type="success" :message="__('messages.no_errors_found')" class="mb-4" />

        <Card>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 text-start dark:bg-gray-700">
                        <tr>
                            <th class="p-2">{{ __('common.student') }}</th>
                            <th class="p-2">{{ __('common.exam') }}</th>
                            <th class="p-2">{{ __('common.marks') }}</th>
                            <th class="p-2">{{ __('labels.notes') }}</th>
                            <th class="p-2">{{ __('common.status') }} / {{ __('common.errors') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="row in preview" :key="row.row" class="border-t dark:border-gray-700">
                            <td class="p-2">{{ row.user_email }}</td>
                            <td class="p-2">{{ row.exam_title }}</td>
                            <td class="p-2">{{ row.marks }}</td>
                            <td class="p-2">{{ row.notes }}</td>
                            <td class="p-2">
                                <ul v-if="row.errors.length" class="text-red-500">
                                    <li v-for="(error, i) in row.errors" :key="i">{{ error }}</li>
                                </ul>
                                <span v-else class="text-green-500">{{ __('messages.ready') }}</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </Card>

        <div class="mt-6 flex justify-end gap-4">
            <BaseButton as="a" :href="route('admin.marks.index')" class="bg-gray-500 hover:bg-gray-600">{{ __('common.cancel') }}</BaseButton>
            <BaseButton @click="submit" :disabled="hasErrors || form.processing"> {{ __('buttons.commit_import') }} </BaseButton>
        </div>
    </div>
</template>
