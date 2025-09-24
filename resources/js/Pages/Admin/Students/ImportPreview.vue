<script setup>
import AdminLayout from '@/Pages/Admin/Layout.vue';
import BaseButton from '@/components/FormElements/BaseButton.vue';
import BaseSelect from '@/components/FormElements/BaseSelect.vue';
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

const commitForm = useForm({
    preview_id: props.preview_id,
    on_duplicate: 'fail', // 'fail', 'skip', or 'update'
});

const hasErrors = computed(() => props.preview.some((row) => row.errors.length > 0));
const canCommit = computed(() => !hasErrors.value || (hasErrors.value && commitForm.on_duplicate !== 'fail'));

const submitCommit = () => {
    commitForm.post(route('admin.students.import.commit'));
};
</script>

<template>
    <div>
        <Head :title="__('admin.confirm_student_import')" />
        <SectionHeader :title="__('admin.confirm_student_import')" />
        <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">{{ __('admin.import_preview_review') }}</p>

        <Alert v-if="hasErrors" type="error" :message="__('messages.file_contains_errors')" class="mb-4" />
        <Alert v-else type="success" :message="__('messages.no_errors_found')" class="mb-4" />

        <Card>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 text-start dark:bg-gray-700">
                        <tr>
                            <th class="p-2">{{ __('common.name') }}</th>
                            <th class="p-2">{{ __('common.email') }}</th>
                            <th class="p-2">{{ __('common.level') }}</th>
                            <th class="p-2">{{ __('common.approved') }}</th>
                            <th class="p-2">{{ __('admin.status_errors') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="row in preview" :key="row.row" class="border-t dark:border-gray-700">
                            <td class="p-2">{{ row.name }}</td>
                            <td class="p-2">{{ row.email }}</td>
                            <td class="p-2">{{ row.level?.name || 'N/A' }}</td>
                            <td class="p-2">{{ row.is_approved ? __('common.yes') : __('common.no') }}</td>
                            <td class="p-2">
                                <ul v-if="row.errors.length" class="text-red-500">
                                    <li v-for="(error, i) in row.errors" :key="i">{{ error }}</li>
                                </ul>
                                <span v-else-if="row.existing_user_id" class="text-yellow-500">
                                    {{
                                        __('admin.existing_user_status', {
                                            policy: __(
                                                'admin.on_duplicate_' +
                                                    commitForm.on_duplicate.replace('(Default)', '').trim().toLowerCase().replace(' ', '_'),
                                            ),
                                        })
                                    }}
                                </span>
                                <span v-else class="text-green-500">{{ __('admin.ready_to_import') }}</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </Card>

        <div class="mt-6 flex flex-col items-center justify-between gap-4 rounded-lg bg-gray-50 p-4 sm:flex-row dark:bg-gray-800">
            <div>
                <label class="font-semibold">{{ __('admin.on_duplicate_label') }}</label>
                <BaseSelect v-model="commitForm.on_duplicate" class="mt-1">
                    <option value="fail">{{ __('admin.on_duplicate_fail') }}</option>
                    <option value="skip">{{ __('admin.on_duplicate_skip') }}</option>
                    <option value="update">{{ __('admin.on_duplicate_update') }}</option>
                </BaseSelect>
            </div>
            <div class="flex gap-4">
                <BaseButton as="a" :href="route('admin.students.import.form')" class="bg-gray-500 hover:bg-gray-600">{{
                    __('common.cancel')
                }}</BaseButton>
                <BaseButton @click="submitCommit" :disabled="!canCommit || commitForm.processing"> {{ __('buttons.commit_import') }} </BaseButton>
            </div>
        </div>
    </div>
</template>
