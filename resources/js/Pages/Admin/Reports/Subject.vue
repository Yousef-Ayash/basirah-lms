<script setup>
import AdminLayout from '@/Pages/Admin/Layout.vue';
import Card from '@/components/LayoutStructure/Card.vue';
import SectionHeader from '@/components/LayoutStructure/SectionHeader.vue';
import { useTranslations } from '@/composables/useTranslations';
import { Link } from '@inertiajs/vue3';

const { __ } = useTranslations();

defineOptions({ layout: AdminLayout });

const props = defineProps({
    subject: Object,
    data: Object,
    pass_threshold: Number,
});
</script>

<template>
    <div>
        <Head :title="__('admin.performance_report_for', { subject: subject.title })" />
        <SectionHeader :title="__('admin.performance_report_for', { subject: subject.title })" />
        <Link :href="route('admin.reports.index')" class="mb-4 block text-sm text-blue-500 hover:underline"
            >&larr; {{ __('common.back_to_reports') }}</Link
        >

        <Card>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 text-start dark:bg-gray-700">
                        <tr>
                            <th class="p-2">{{ __('admin.exam_title') }}</th>
                            <th class="p-2">{{ __('student.average_score') }}</th>
                            <th class="p-2">{{ __('admin.total_attempts') }}</th>
                            <th class="p-2">{{ __('admin.passed_threshold', { threshold: pass_threshold }) }}</th>
                            <th class="p-2">{{ __('admin.pass_rate') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="examReport in data" :key="examReport.exam_id" class="border-t dark:border-gray-700">
                            <td class="p-2">{{ examReport.exam.title }}</td>
                            <td class="p-2 font-semibold">{{ examReport.avg_score || 'N/A' }}%</td>
                            <td class="p-2">{{ examReport.attempts }}</td>
                            <td class="p-2">{{ examReport.passed }}</td>
                            <td class="p-2 font-semibold">{{ examReport.pass_rate || 'N/A' }}%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </Card>
    </div>
</template>
