<script setup>
import AdminLayout from '@/Pages/Admin/Layout.vue';
import BaseButton from '@/components/FormElements/BaseButton.vue';
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
    exams: Object,
    subjects: Array,
    filters: Object,
});

const filters = reactive({
    q: props.filters.q || '',
    subject_id: props.filters.subject_id || '',
});

watch(
    filters,
    (newFilters) => {
        router.get(route('admin.exams.index'), newFilters, {
            preserveState: true,
            replace: true,
        });
    },
    { deep: true },
);
</script>

<template>
    <div>
        <Head :title="__('admin.manage_exams')" />
        <SectionHeader :title="__('common.exams')">
            <template #action>
                <BaseButton as="a" :href="route('admin.exams.create')">{{ __('buttons.new_exam') }}</BaseButton>
            </template>
        </SectionHeader>

        <Card class="mb-4">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <BaseInput v-model="filters.q" :placeholder="__('common.search_by_title')" />
                <BaseSelect v-model="filters.subject_id">
                    <option value="">{{ __('labels.filter_by_subject') }}</option>
                    <option v-for="subject in subjects" :key="subject.id" :value="subject.id">
                        {{ subject.title }}
                    </option>
                </BaseSelect>
            </div>
        </Card>

        <Card>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 text-start dark:bg-gray-700">
                        <tr>
                            <th class="p-2">{{ __('common.title') }}</th>
                            <th class="p-2">{{ __('common.subject') }}</th>
                            <th class="p-2">{{ __('labels.max_attempts') }}</th>
                            <th class="p-2">{{ __('common.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="exam in exams.data" :key="exam.id" class="border-t dark:border-gray-700">
                            <td class="p-2">{{ exam.title }}</td>
                            <td class="p-2">{{ exam.subject.title }}</td>
                            <td class="p-2">{{ exam.max_attempts }}</td>
                            <td class="p-2 whitespace-nowrap">
                                <BaseButton as="a" :href="route('admin.exams.show', exam.id)" class="me-2">{{ __('common.view') }}</BaseButton>
                                <BaseButton as="a" :href="route('admin.exams.edit', exam.id)" class="bg-blue-500 hover:bg-blue-600">{{
                                    __('common.edit')
                                }}</BaseButton>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </Card>
        <div class="mt-6">
            <Pagination :links="exams.links" />
        </div>
    </div>
</template>
