<script setup>
import AdminLayout from '@/Pages/Admin/Layout.vue';
import Card from '@/components/LayoutStructure/Card.vue';
import SectionHeader from '@/components/LayoutStructure/SectionHeader.vue';
import { useTranslations } from '@/composables/useTranslations';
import { Link } from '@inertiajs/vue3';

const { __ } = useTranslations();

defineOptions({ layout: AdminLayout });

const props = defineProps({
    data: Object,
    user: Object,
    exam: Object,
});
</script>

<template>
    <div>
        <Head :title="__('admin.student_exam_report') + `: ${user.name}`" />
        <SectionHeader>
            <template #title>
                <div class="flex flex-wrap items-center space-x-2 text-xl">
                    <span>{{ __('admin.report_for') }}</span>
                    <span class="font-semibold text-blue-500">{{ user.name }}</span>
                    <span>{{ __('admin.report_on') }}</span>
                    <span class="font-semibold text-green-500">{{ exam.title }}</span>
                </div>
            </template>
        </SectionHeader>
        <Link :href="route('admin.reports.index')" class="mb-4 block text-sm text-blue-500 hover:underline"
            >&larr; {{ __('common.back_to_reports') }}</Link
        >

        <div class="grid grid-cols-2 gap-4 text-center md:grid-cols-4">
            <Card>
                <div class="text-gray-500">{{ __('admin.total_attempts') }}</div>
                <div class="text-2xl font-bold">{{ data.attempts }}</div>
            </Card>
            <Card>
                <div class="text-gray-500">{{ __('student.average_score') }}</div>
                <div class="text-2xl font-bold">{{ data.avg_score || 'N/A' }}%</div>
            </Card>
            <Card>
                <div class="text-gray-500">{{ __('admin.best_score') }}</div>
                <div class="text-2xl font-bold">{{ data.best_score || 'N/A' }}%</div>
            </Card>
            <Card>
                <div class="text-gray-500">{{ __('admin.last_score') }}</div>
                <div class="text-2xl font-bold">{{ data.last_score || 'N/A' }}%</div>
            </Card>
        </div>
    </div>
</template>
