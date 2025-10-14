<template>
    <div>
        <Head title="إدارة المواد" />
        <SectionHeader title="المواد الدراسية">
            <template #action>
                <BaseButton as="a" :href="route('admin.subjects.create')">
                    + مادة جديدة
                </BaseButton>
            </template>
        </SectionHeader>

        <Card class="mb-4">
            <BaseInput
                v-model="search"
                placeholder="البحث عن طريق العنوان..."
                class="w-full sm:w-2/3"
            />
        </Card>

        <Card v-if="subjects.data.length" class="space-y-2">
            <div
                v-for="subject in subjects.data"
                :key="subject.id"
                class="flex flex-col items-start justify-between border-b p-4 last:border-b-0 sm:flex-row sm:items-center dark:border-gray-700"
            >
                <div>
                    <h3 class="font-medium text-gray-900 dark:text-white">
                        {{ subject.title }}
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        المستوى: {{ subject.level?.name || 'N/A' }} | مواد:
                        {{ subject.materials_count }} | الاختبارات:
                        {{ subject.exams_count }}
                    </p>
                </div>
                <div class="mt-2 flex items-center gap-2 sm:mt-0">
                    <BaseButton
                        as="a"
                        :href="route('admin.subjects.edit', subject.id)"
                        >تعديل</BaseButton
                    >
                    <BaseButton
                        class="bg-red-500 text-white hover:bg-red-600"
                        @click="confirmDelete(subject)"
                    >
                        حذف
                    </BaseButton>
                </div>
            </div>
        </Card>
        <EmptyState v-else message="لم يتم العثور على مواد دراسية." />

        <div class="mt-6">
            <Pagination :links="subjects.links" />
        </div>

        <ConfirmDialog
            :show="showConfirm"
            title="حذف المادة"
            :message="`هل أنت متأكد من أنك تريد حذف '${subjectToDelete?.title}'؟ لا يمكن التراجع عن هذا الإجراء.`"
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

import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

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
        router.delete(
            route('admin.subjects.destroy', subjectToDelete.value.id),
            {
                onFinish: () => {
                    showConfirm.value = false;
                    subjectToDelete.value = null;
                },
            },
        );
    }
};
</script>
