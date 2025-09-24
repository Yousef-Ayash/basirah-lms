<script>
import AdminLayout from '@/Pages/Admin/Layout.vue';
import StudentLayout from '@/Pages/Student/Layout.vue';
import { usePage, Head } from '@inertiajs/vue3';

export default {
    layout: (h, page) => {
        const user = usePage().props.auth.user;
        const isAdmin = user.roles?.some((role) => role.name === 'admin');
        const LayoutComponent = isAdmin ? AdminLayout : StudentLayout;
        return h(LayoutComponent, [page]);
    },
};
</script>

<script setup>
import BaseButton from '@/components/FormElements/BaseButton.vue';
import Card from '@/components/LayoutStructure/Card.vue';
import Pagination from '@/components/LayoutStructure/Pagination.vue';
import SectionHeader from '@/components/LayoutStructure/SectionHeader.vue';
import EmptyState from '@/components/Misc/EmptyState.vue';
import { useTranslations } from '@/composables/useTranslations';
import { Link } from '@inertiajs/vue3';

const { __ } = useTranslations();

const props = defineProps({
    attempts: Object, // Paginated attempts object
});
</script>

<template>
    <div>
        <Head :title="__('student.my_marks_and_attempts')" />
        <SectionHeader :title="__('student.exam_history')">
            <template #action>
                <BaseButton as="a" :href="route('attempts.export')" class="bg-gray-700 hover:bg-gray-800"> {{ __('buttons.export_marks') }} </BaseButton>
            </template>
        </SectionHeader>

        <Card v-if="attempts.data.length">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 text-start dark:bg-gray-700">
                        <tr>
                            <th class="p-2">{{ __('common.exam_title') }}</th>
                            <th class="p-2">{{ __('common.score') }}</th>
                            <th class="p-2">{{ __('common.submitted_at') }}</th>
                            <th class="p-2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="attempt in attempts.data" :key="attempt.id" class="border-t dark:border-gray-700">
                            <td class="p-2">{{ attempt.exam.title }}</td>
                            <td class="p-2 font-semibold">
                                <span v-if="attempt.score !== null">{{ attempt.score }}%</span>
                                <span v-else class="text-gray-400">{{ __('common.pending') }}</span>
                            </td>
                            <td class="p-2">{{ new Date(attempt.submitted_at).toLocaleString() }}</td>
                            <td class="p-2 text-end">
                                <Link :href="route('attempts.show', attempt.id)" class="text-blue-500 hover:underline"> {{ __('student.view_details') }} </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </Card>
        <EmptyState v-else :message="__('student.no_exams_completed')" />

        <div class="mt-6">
            <Pagination :links="attempts.links" />
        </div>
    </div>
</template>
