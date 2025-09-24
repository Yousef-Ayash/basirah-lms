<template>
    <div>
        <Head :title="__('admin.manage_subjects')" />
        <SectionHeader :title="__('common.subjects')">
            <template #action>
                <BaseButton as="a" :href="route('admin.subjects.create')"> {{ __('buttons.new_subject') }} </BaseButton>
            </template>
        </SectionHeader>

        <div class="mb-4">
            <BaseInput v-model="search" :placeholder="__('common.search_by_title')" class="w-full sm:w-1/2" />
        </div>

        <Card v-if="subjects.data.length" class="space-y-2">
            <div
                v-for="subject in subjects.data"
                :key="subject.id"
                class="flex flex-col items-start justify-between border-b p-4 last:border-b-0 sm:flex-row sm:items-center dark:border-gray-700"
            >
                <div>
                    <h3 class="font-medium text-gray-900 dark:text-white">{{ subject.title }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ __('common.level') }}: {{ subject.level?.name || 'N/A' }} | {{ __('common.materials') }}: {{ subject.materials_count }} |
                        {{ __('common.exams') }}: {{ subject.exams_count }}
                    </p>
                </div>
                <div class="mt-2 flex items-center gap-2 sm:mt-0">
                    <BaseButton as="a" :href="route('admin.subjects.edit', subject.id)">{{ __('common.edit') }}</BaseButton>
                    <BaseButton class="bg-red-500 text-white hover:bg-red-600" @click="confirmDelete(subject)">
                        {{ __('common.delete') }}
                    </BaseButton>
                </div>
            </div>
        </Card>

        <EmptyState v-else :message="__('messages.no_subjects_found')" />

        <div class="mt-6">
            <Pagination :links="subjects.links" />
        </div>

        <ConfirmDialog
            :show="showConfirm"
            :title="__('admin.delete_subject')"
            :message="__('messages.delete_confirm', { item: subjectToDelete?.title })"
            @confirm="deleteSubject"
            @cancel="showConfirm = false"
        />
    </div>
</template>

<script setup>
import AdminLayout from '@/Pages/Admin/Layout.vue';
import BaseButton from '@/components/FormElements/BaseButton.vue';
import BaseInput from '@/components/FormElements/BaseInput.vue';
import Card from '@/components/LayoutStructure/Card.vue';
import Pagination from '@/components/LayoutStructure/Pagination.vue';
import SectionHeader from '@/components/LayoutStructure/SectionHeader.vue';
import ConfirmDialog from '@/components/Misc/ConfirmDialog.vue';
import EmptyState from '@/components/Misc/EmptyState.vue';
import { useTranslations } from '@/composables/useTranslations';
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const { __ } = useTranslations();

defineOptions({ layout: AdminLayout });

const props = defineProps({
    subjects: Object,
    filters: Object,
});

const showConfirm = ref(false);
const subjectToDelete = ref(null);
const search = ref(props.filters.q);

watch(search, (value) => {
    router.get(
        route('admin.subjects.index'),
        { q: value },
        {
            preserveState: true,
            replace: true,
        },
    );
});

const confirmDelete = (subject) => {
    subjectToDelete.value = subject;
    showConfirm.value = true;
};

const deleteSubject = () => {
    if (subjectToDelete.value) {
        router.delete(route('admin.subjects.destroy', subjectToDelete.value.id), {
            onFinish: () => {
                showConfirm.value = false;
                subjectToDelete.value = null;
            },
        });
    }
};
</script>
