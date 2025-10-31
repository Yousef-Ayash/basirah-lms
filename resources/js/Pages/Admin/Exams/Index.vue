<script setup>
import AdminLayout from '@/Pages/Admin/Layout.vue';
import BaseButton from '@/components/FormElements/BaseButton.vue';
import BaseInput from '@/components/FormElements/BaseInput.vue';
import BaseSelect from '@/components/FormElements/BaseSelect.vue';
import Card from '@/components/LayoutStructure/Card.vue';
import Pagination from '@/components/LayoutStructure/Pagination.vue';
import SectionHeader from '@/components/LayoutStructure/SectionHeader.vue';
import ConfirmDialog from '@/components/Misc/ConfirmDialog.vue';
import { Head, router } from '@inertiajs/vue3';
import { reactive, watch, ref } from 'vue';
import EmptyState from '@/components/Misc/EmptyState.vue';

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

const showConfirm = ref(false);
const examToDelete = ref(null);

const confirmDelete = (exam) => {
    examToDelete.value = exam;
    showConfirm.value = true;
};

const deleteExam = () => {
    if (examToDelete.value) {
        router.delete(route('admin.exams.destroy', examToDelete.value.id), {
            onFinish: () => {
                showConfirm.value = false;
                examToDelete.value = null;
            },
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <div>
        <Head title="إدارة الاختبارات" />
        <SectionHeader title="الاختبارات">
            <template #action>
                <BaseButton as="a" :href="route('admin.exams.create')"
                    >+ اختبار جديد</BaseButton
                >
            </template>
        </SectionHeader>

        <Card class="mb-4">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <BaseInput
                    v-model="filters.q"
                    placeholder="البحث عن طريق العنوان..."
                />
                <BaseSelect v-model="filters.subject_id">
                    <option value="">تصفية حسب المادة...</option>
                    <option
                        v-for="subject in subjects"
                        :key="subject.id"
                        :value="subject.id"
                    >
                        {{ subject.title }}
                    </option>
                </BaseSelect>
            </div>
        </Card>

        <Card v-if="exams.data.length" class="space-y-2">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 text-right dark:bg-gray-700">
                        <tr>
                            <th class="p-2">العنوان</th>
                            <th class="p-2">المادة</th>
                            <th class="p-2">أقصى عدد للمحاولات</th>
                            <th class="p-2">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="exam in exams.data"
                            :key="exam.id"
                            class="border-t dark:border-gray-700"
                        >
                            <td class="p-2">{{ exam.title }}</td>
                            <td class="p-2">{{ exam.subject.title }}</td>
                            <td class="p-2">{{ exam.max_attempts }}</td>
                            <td class="p-2 whitespace-nowrap">
                                <BaseButton
                                    as="a"
                                    :href="route('admin.exams.show', exam.id)"
                                    class="me-2"
                                    >عرض</BaseButton
                                >
                                <BaseButton
                                    as="a"
                                    :href="route('admin.exams.edit', exam.id)"
                                    class="bg-blue-500 hover:bg-blue-600"
                                    >تعديل</BaseButton
                                >
                                <BaseButton
                                    @click="confirmDelete(exam)"
                                    class="ms-2 bg-red-500 hover:bg-red-600"
                                >
                                    حذف
                                </BaseButton>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </Card>
        <EmptyState v-else message="لا يوجد اختبارات لعرضها." />

        <div class="mt-6">
            <Pagination :links="exams.links" />
        </div>

        <ConfirmDialog
            :show="showConfirm"
            title="حذف الاختبار"
            @confirm="deleteExam"
            @cancel="showConfirm = false"
        >
            <p>
                هل أنت متأكد من حذف الاختبار
                <strong class="text-red-600">
                    {{ examToDelete?.title }}
                </strong>
                من المادة
                <strong> {{ examToDelete?.subject.title }} </strong>؟ لا يمكن
                التراجع عن هذا الإجراء.
            </p>
        </ConfirmDialog>
    </div>
</template>
