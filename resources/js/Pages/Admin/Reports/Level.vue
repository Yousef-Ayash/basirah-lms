<script setup>
import AdminLayout from '@/Pages/Admin/Layout.vue';
import Card from '@/components/LayoutStructure/Card.vue';
import SectionHeader from '@/components/LayoutStructure/SectionHeader.vue';
import { useTranslations } from '@/composables/useTranslations';
import { Link } from '@inertiajs/vue3';

const { __ } = useTranslations();

defineOptions({ layout: AdminLayout });

const props = defineProps({
    level: Object,
    result: Object,
    pass_threshold: Number,
});
</script>

<template>
    <div>
        <Head :title="__('admin.level_summary') + `: ${level.name}`" />
        <SectionHeader :title="__('admin.summary_report_for', { level: level.name })" />
        <Link :href="route('admin.reports.index')" class="mb-4 block text-sm text-blue-500 hover:underline"
            >&larr; {{ __('common.back_to_reports') }}</Link
        >

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <Card class="text-center">
                <div class="text-gray-500">{{ __('admin.overall_attempts_in_level') }}</div>
                <div class="text-3xl font-bold">{{ result.overall.attempts }}</div>
            </Card>
            <Card class="text-center">
                <div class="text-gray-500">{{ __('admin.overall_pass_rate', { threshold: pass_threshold }) }}</div>
                <div class="text-3xl font-bold text-green-500">{{ result.overall.pass_rate || 'N/A' }}%</div>
            </Card>
        </div>

        <h3 class="mt-6 mb-2 font-semibold">{{ __('admin.breakdown_by_subject') }}</h3>
        <Card>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 text-start dark:bg-gray-700">
                        <tr>
                            <th class="p-2">{{ __('admin.subject_title') }}</th>
                            <th class="p-2">{{ __('student.average_score') }}</th>
                            <th class="p-2">{{ __('admin.total_attempts') }}</th>
                            <th class="p-2">{{ __('admin.pass_rate') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="subjectReport in result.subjects" :key="subjectReport.subject_id" class="border-t dark:border-gray-700">
                            <td class="p-2">{{ subjectReport.subject_title }}</td>
                            <td class="p-2 font-semibold">{{ subjectReport.avg_score || 'N/A' }}%</td>
                            <td class="p-2">{{ subjectReport.attempts }}</td>
                            <td class="p-2 font-semibold">{{ subjectReport.pass_rate || 'N/A' }}%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </Card>
    </div>
</template>
