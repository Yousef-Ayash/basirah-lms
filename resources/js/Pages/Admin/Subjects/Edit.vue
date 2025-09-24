<template>
    <div>
        <Head :title="__('admin.edit_subject', { title: subject.title })" />
        <h1 class="mb-4 text-2xl font-bold">{{ __('admin.edit_subject', { title: subject.title }) }}</h1>

        <TabGroup :tabs="tabLabels" v-slot="{ active }">
            <div v-show="active === 0">
                <form @submit.prevent="updateSubjectDetails">
                    <SubjectForm v-model="form" :levels="levels" />
                    <div class="mt-4 flex justify-end">
                        <BaseButton type="submit" :disabled="form.processing"> {{ __('buttons.save_changes') }} </BaseButton>
                    </div>
                </form>
            </div>

            <div v-show="active === 1" class="space-y-6">
                <AddMaterialForm :subject-id="subject.id" />

                <SectionHeader :title="__('admin.existing_materials')" />
                <Card v-if="subject.materials.length">
                    <ul class="divide-y dark:divide-gray-700">
                        <li v-for="material in subject.materials" :key="material.id" class="flex items-center justify-between p-2">
                            <span>{{ material.title }} ({{ material.type }})</span>
                            <div class="flex gap-2">
                                <BaseButton as="a" :href="route('materials.show', material.id)" target="_blank" class="bg-gray-500 hover:bg-gray-600">
                                    {{ __('common.view') }}
                                </BaseButton>
                                <Link
                                    :href="route('admin.materials.destroy', material.id)"
                                    method="delete"
                                    as="button"
                                    class="inline-flex items-center justify-center rounded-lg bg-red-500 px-4 py-2 font-semibold text-white transition hover:bg-red-600"
                                    preserve-scroll
                                >
                                    {{ __('common.delete') }}
                                </Link>
                            </div>
                        </li>
                    </ul>
                </Card>
                <EmptyState v-else :message="__('messages.no_materials_yet')" />
            </div>

            <div v-show="active === 2" class="space-y-6">
                <SectionHeader :title="__('admin.exams_for_subject')">
                    <template #action>
                        <BaseButton as="a" :href="route('admin.exams.create', { subject_id: subject.id })"> {{ __('buttons.new_exam') }} </BaseButton>
                    </template>
                </SectionHeader>

                <Card v-if="subject.exams.length">
                    <ul class="divide-y dark:divide-gray-700">
                        <li v-for="exam in subject.exams" :key="exam.id" class="flex items-center justify-between p-2">
                            <span>{{ exam.title }}</span>
                            <div class="flex gap-2">
                                <BaseButton as="a" :href="route('admin.exams.show', exam.id)">{{ __('admin.manage_questions') }}</BaseButton>
                                <BaseButton as="a" :href="route('admin.exams.edit', exam.id)" class="bg-blue-500 hover:bg-blue-600">
                                    {{ __('buttons.edit_details') }}
                                </BaseButton>
                            </div>
                        </li>
                    </ul>
                </Card>
                <EmptyState v-else :message="__('messages.no_exams_yet')" />
            </div>
        </TabGroup>
    </div>
</template>

<script setup>
import AdminLayout from '@/Pages/Admin/Layout.vue';
import BaseButton from '@/components/FormElements/BaseButton.vue';
import Card from '@/components/LayoutStructure/Card.vue';
import SectionHeader from '@/components/LayoutStructure/SectionHeader.vue';
import TabGroup from '@/components/LayoutStructure/TabGroup.vue';
import AddMaterialForm from '@/components/Materials/AddMaterialForm.vue';
import EmptyState from '@/components/Misc/EmptyState.vue';
import SubjectForm from '@/components/Subjects/SubjectForm.vue';
import { useTranslations } from '@/composables/useTranslations';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const { __ } = useTranslations();

defineOptions({ layout: AdminLayout });

const props = defineProps({
    subject: Object,
    levels: Array,
});

const form = useForm({
    title: props.subject.title,
    description: props.subject.description,
    level_id: props.subject.level_id,
    cover: null,
});

const tabLabels = computed(() => [__('common.details'), __('common.materials'), __('common.exams')]);

const updateSubjectDetails = () => {
    form.put(route('admin.subjects.update', props.subject.id), {
        _method: 'put',
        onSuccess: () => {
            form.reset('cover');
        },
    });
};
</script>
