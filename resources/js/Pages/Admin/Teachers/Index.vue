<script setup>
import AdminLayout from '@/Pages/Admin/Layout.vue';
import { Head, Link } from '@inertiajs/vue3';
import SectionHeader from '@/components/LayoutStructure/SectionHeader.vue';
import BaseButton from '@/components/FormElements/BaseButton.vue';
import Card from '@/components/LayoutStructure/Card.vue';
import Pagination from '@/components/LayoutStructure/Pagination.vue';
import { useTranslations } from '@/composables/useTranslations';

const { __ } = useTranslations();

defineOptions({ layout: AdminLayout });

defineProps({
    teachers: Object,
});
</script>

<template>
    <div>
        <Head :title="__('admin.manager_teachers')" />
        <SectionHeader :title="__('common.teachers')">
            <template #action>
                <BaseButton as="a" :href="route('admin.teachers.create')">{{
                    __('buttons.add_teacher')
                }}</BaseButton>
            </template>
        </SectionHeader>

        <Card>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-100 text-start dark:bg-gray-700">
                            <th class="p-2">{{ __('common.photo') }}</th>
                            <th class="p-2">{{ __('common.name') }}</th>
                            <th class="p-2">{{ __('common.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="teacher in teachers.data"
                            :key="teacher.id"
                            class="border-t dark:border-gray-700"
                        >
                            <td class="p-2">
                                <img
                                    :src="teacher.photo_url"
                                    :alt="teacher.name"
                                    class="h-10 w-10 rounded-full object-cover"
                                />
                            </td>
                            <td class="p-2">{{ teacher.name }}</td>
                            <td class="p-2">
                                <BaseButton
                                    as="a"
                                    :href="
                                        route('admin.teachers.edit', teacher.id)
                                    "
                                    class="bg-blue-500 hover:bg-blue-600"
                                    >{{ __('common.edit') }}</BaseButton
                                >
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </Card>
        <div class="mt-6">
            <Pagination :links="teachers.links" />
        </div>
    </div>
</template>
