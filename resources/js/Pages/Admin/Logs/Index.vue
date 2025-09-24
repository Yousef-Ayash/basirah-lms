<script setup>
import AdminLayout from '@/Pages/Admin/Layout.vue';
import BaseInput from '@/components/FormElements/BaseInput.vue';
import BaseSelect from '@/components/FormElements/BaseSelect.vue';
import Card from '@/components/LayoutStructure/Card.vue';
import Pagination from '@/components/LayoutStructure/Pagination.vue';
import SectionHeader from '@/components/LayoutStructure/SectionHeader.vue';
import { useTranslations } from '@/composables/useTranslations';
import { Head, router } from '@inertiajs/vue3';
import { reactive, watch } from 'vue';

const { __ } = useTranslations();

defineOptions({ layout: AdminLayout });

const props = defineProps({
    logs: Object, // Paginated logs object
    filters: Object,
    users: Array,
    exams: Array,
});

const filters = reactive({
    user_id: props.filters.user_id || '',
    exam_id: props.filters.exam_id || '',
    action: props.filters.action || '',
    ip: props.filters.ip || '',
    date_from: props.filters.date_from || '',
    date_to: props.filters.date_to || '',
});

watch(
    filters,
    (newFilters) => {
        router.get(route('admin.logs.exam.index'), newFilters, {
            preserveState: true,
            replace: true,
        });
    },
    { deep: true },
);
</script>

<template>
    <div>
        <Head :title="__('admin.exam_logs')" />
        <SectionHeader :title="__('admin.exam_audit_logs')" />

        <Card class="mb-4">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <BaseSelect v-model="filters.user_id">
                    <option value="">{{ __('labels.filter_by_student_placeholder') }}</option>
                    <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                </BaseSelect>
                <BaseSelect v-model="filters.exam_id">
                    <option value="">{{ __('labels.filter_by_exam_placeholder') }}</option>
                    <option v-for="exam in exams" :key="exam.id" :value="exam.id">{{ exam.title }}</option>
                </BaseSelect>
                <BaseSelect v-model="filters.action">
                    <option value="">{{ __('labels.filter_by_action') }}</option>
                    <option value="start">{{ __('labels.action_start') }}</option>
                    <option value="autosave">{{ __('labels.action_autosave') }}</option>
                    <option value="submit">{{ __('labels.action_submit') }}</option>
                </BaseSelect>
                <BaseInput v-model="filters.ip" :placeholder="__('labels.filter_by_ip')" />
                <BaseInput type="date" :label="__('labels.date_from')" v-model="filters.date_from" />
                <BaseInput type="date" :label="__('labels.date_to')" v-model="filters.date_to" />
            </div>
        </Card>

        <Card>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 text-start dark:bg-gray-700">
                        <tr>
                            <th class="p-2">{{ __('common.user') }}</th>
                            <th class="p-2">{{ __('common.action') }}</th>
                            <th class="p-2">{{ __('common.exam') }}</th>
                            <th class="p-2">{{ __('labels.ip_address') }}</th>
                            <th class="p-2">{{ __('common.timestamp') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="log in logs.data" :key="log.id" class="border-t dark:border-gray-700">
                            <td class="p-2">{{ log.user?.name || 'N/A' }}</td>
                            <td class="p-2">
                                <span class="rounded bg-gray-200 px-2 py-1 font-mono text-xs dark:bg-gray-700">{{ log.action }}</span>
                            </td>
                            <td class="p-2">{{ log.attempt?.exam?.title || 'N/A' }}</td>
                            <td class="p-2">{{ log.ip }}</td>
                            <td class="p-2">{{ new Date(log.created_at).toLocaleString() }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </Card>

        <div class="mt-6">
            <Pagination :links="logs.links" />
        </div>
    </div>
</template>
