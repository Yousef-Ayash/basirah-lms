<script setup>
import StudentLayout from '@/Pages/Student/Layout.vue';
import BaseButton from '@/components/FormElements/BaseButton.vue';
import Card from '@/components/LayoutStructure/Card.vue';
import SectionHeader from '@/components/LayoutStructure/SectionHeader.vue';
import EmptyState from '@/components/Misc/EmptyState.vue';
import { useTranslations } from '@/composables/useTranslations';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const { __ } = useTranslations();

defineOptions({ layout: StudentLayout });

// The user's name is available globally from Inertia's shared props
const studentName = computed(() => usePage().props.auth.user.name);

// The component receives all its data from the DashboardController
const props = defineProps({
    summary: Object,
    upcomingExams: Array,
    recentMaterials: Array,
});

// Navigate to the exam page when "Start" is clicked
const startExam = (exam) => {
    router.get(route('exams.show', { exam: exam.id }));
};

// Navigate to the material viewer page
const viewMaterial = (material) => {
    window.open(material.preview_url, '_blank');
};
</script>

<template>
    <div class="min-h-screen space-y-8">
        <Head :title="__('common.dashboard')" />
        <SectionHeader :title="__('common.welcome_name', { name: studentName })" />

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <Card class="p-6 text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('student.enrolled_subjects') }}</p>
                <h2 class="text-3xl font-bold text-[#61CE70]">{{ summary.enrolledSubjects }}</h2>
            </Card>
            <Card class="p-6 text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('student.exams_taken') }}</p>
                <h2 class="text-3xl font-bold text-[#61CE70]">{{ summary.examsTaken }}</h2>
            </Card>
            <Card class="p-6 text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('student.average_score') }}</p>
                <h2 class="text-3xl font-bold text-[#61CE70]">{{ summary.averageScore }}%</h2>
            </Card>
        </div>

        <SectionHeader :title="__('student.upcoming_exams')" />
        <Card v-if="upcomingExams.length">
            <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                <li v-for="exam in upcomingExams" :key="exam.id" class="flex items-center justify-between py-3">
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">{{ exam.title }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ exam.subject.title }}</p>
                    </div>
                    <BaseButton @click="startExam(exam)">{{ __('student.view_exam') }}</BaseButton>
                </li>
            </ul>
        </Card>
        <EmptyState v-else :message="__('student.no_upcoming_exams')" />

        <SectionHeader :title="__('student.recent_materials')" />
        <Card v-if="recentMaterials.length" class="space-y-4">
            <div
                v-for="mat in recentMaterials"
                :key="mat.id"
                class="flex items-center justify-between border-b border-gray-200 pb-3 last:border-none dark:border-gray-700"
            >
                <div class="flex items-center space-x-3">
                    <span v-if="mat.type === 'pdf'" class="rounded bg-red-100 px-2 py-1 text-xs font-medium text-red-600">PDF</span>
                    <span v-else-if="mat.type === 'picture'" class="rounded bg-blue-100 px-2 py-1 text-xs font-medium text-blue-600">Image</span>
                    <span v-else-if="mat.type === 'youtube'" class="rounded bg-yellow-100 px-2 py-1 text-xs font-medium text-yellow-600"
                        >YouTube</span
                    >
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">{{ mat.title }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ mat.subject.title }}</p>
                    </div>
                </div>
                <BaseButton size="sm" @click="viewMaterial(mat)">{{ __('common.view') }}</BaseButton>
            </div>

            <div class="text-end">
                <Link :href="route('subjects.index')" class="text-sm text-[#61CE70] hover:underline">{{ __('student.view_all_materials') }}</Link>
            </div>
        </Card>
        <EmptyState v-else :message="__('student.no_recent_materials')" />
    </div>
</template>
