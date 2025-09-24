<template>
    <div class="space-y-10">
        <Head :title="__('admin.admin_dashboard')" />
        <SectionHeader :title="__('admin.admin_dashboard')" />

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3">
            <Link v-for="card in summaryCards" :key="card.label" :href="card.href">
                <Card class="text-center transition hover:border-gray-300 dark:hover:border-gray-600">
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ card.label }}</p>
                    <h2 class="text-3xl font-bold text-[#61CE70]">{{ card.value }}</h2>
                </Card>
            </Link>
        </div>

        <SectionHeader :title="__('admin.recent_students')" />
        <Card v-if="recentStudents.length">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 text-start dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-2">{{ __('common.name') }}</th>
                            <th class="px-4 py-2">{{ __('common.email') }}</th>
                            <th class="px-4 py-2">{{ __('common.approved') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="student in recentStudents" :key="student.id" class="border-t border-gray-200 dark:border-gray-700">
                            <td class="px-4 py-2">{{ student.name }}</td>
                            <td class="px-4 py-2">{{ student.email }}</td>
                            <td class="px-4 py-2">
                                <span :class="student.is_approved ? 'text-green-500' : 'text-yellow-500'">
                                    {{ student.is_approved ? __('common.yes') : __('common.pending') }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </Card>
        <EmptyState v-else :message="__('admin.no_recent_students')" />

        <SectionHeader :title="__('admin.quick_actions')" />
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <Link :href="route('admin.subjects.create')">
                <Card class="h-full transition hover:border-gray-300 dark:hover:border-gray-600">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('admin.create_subject') }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('admin.organize_new_topics') }}</p>
                </Card>
            </Link>
            <Link :href="route('admin.students.index')">
                <Card class="h-full transition hover:border-gray-300 dark:hover:border-gray-600">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('admin.manage_students') }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('admin.approve_edit_import') }}</p>
                </Card>
            </Link>
            <Link :href="route('admin.exams.index')">
                <Card class="h-full transition hover:border-gray-300 dark:hover:border-gray-600">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('admin.manage_exams') }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('admin.build_manage_exams') }}</p>
                </Card>
            </Link>
        </div>
    </div>
</template>

<script setup>
import AdminLayout from '@/Pages/Admin/Layout.vue';
import Card from '@/components/LayoutStructure/Card.vue';
import SectionHeader from '@/components/LayoutStructure/SectionHeader.vue';
import EmptyState from '@/components/Misc/EmptyState.vue';
import { useTranslations } from '@/composables/useTranslations';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

const { __ } = useTranslations();

// Set the layout for this page
defineOptions({ layout: AdminLayout });

// The dashboard now receives all its data directly from the controller via props
const props = defineProps({
    stats: Object,
    recentStudents: Array,
});

// The summary card data is now derived from the 'stats' prop
const summaryCards = computed(() => [
    {
        label: __('admin.total_students'),
        value: props.stats.studentsCount,
        href: route('admin.students.index'),
    },
    {
        label: __('admin.total_subjects'),
        value: props.stats.subjectsCount,
        href: route('admin.subjects.index'),
    },
    {
        label: __('admin.pending_approvals'),
        value: props.stats.pendingCount,
        href: route('admin.students.index', { is_approved: '0' }), // Link to a filtered view
    },
]);
</script>
