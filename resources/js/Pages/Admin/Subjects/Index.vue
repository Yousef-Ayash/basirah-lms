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
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <BaseInput
                    type="text"
                    label="البحث بالعنوان"
                    v-model="filters.title"
                    placeholder="عنوان المادة..."
                />
                <BaseSelect
                    v-model="filters.course_id"
                    label="تصفية حسب المقرر"
                >
                    <option value="">تصفية حسب المقرر...</option>
                    <option
                        v-for="course in courses"
                        :key="course.id"
                        :value="course.id"
                    >
                        {{ course.title }}
                    </option>
                </BaseSelect>
                <BaseSelect
                    v-model="filters.level_id"
                    label="تصفية حسب المستوى"
                >
                    <option value="">تصفية حسب المستوى...</option>
                    <option
                        v-for="level in levels"
                        :key="level.id"
                        :value="level.id"
                    >
                        {{ level.name }}
                    </option>
                </BaseSelect>
                <BaseSelect
                    v-model="filters.teacher_id"
                    label="تصفية حسب المدرس"
                >
                    <option value="">تصفية حسب المدرس...</option>
                    <option
                        v-for="teacher in teachers"
                        :key="teacher.id"
                        :value="teacher.id"
                    >
                        {{ teacher.name }}
                    </option>
                </BaseSelect>
            </div>
        </Card>

        <Card v-if="subjects.data.length" class="space-y-2">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 text-right dark:bg-gray-700">
                        <tr>
                            <th class="p-2">عنوان المادة</th>
                            <th class="p-2">المستوى</th>
                            <th class="p-2">المقرر</th>
                            <th class="p-2">المدرس</th>
                            <th class="p-2">الملفات</th>
                            <th class="p-2">المحاضرات</th>
                            <th class="p-2">الاختبارات</th>
                            <th class="p-2">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="subject in subjects.data"
                            :key="subject.id"
                            class="border-t border-gray-200 dark:border-gray-700"
                        >
                            <td class="p-2 font-bold whitespace-nowrap">
                                {{ subject.title }}
                            </td>
                            <td class="p-2">
                                {{ subject.level?.name || 'N/A' }}
                            </td>
                            <td class="p-2">
                                {{ subject.course?.title || '—' }}
                            </td>
                            <td class="p-2">
                                {{
                                    subject.teacher?.name ||
                                    'لا يوجد مدرس للمادة'
                                }}
                            </td>
                            <td class="p-2">
                                {{ subject.non_youtube_materials_count }}
                            </td>
                            <td class="p-2">
                                {{ subject.youtube_materials_count }}
                            </td>
                            <td class="p-2">{{ subject.exams_count }}</td>
                            <td class="space-x-2 p-2 whitespace-nowrap">
                                <BaseButton
                                    class="bg-blue-500 hover:bg-blue-600"
                                    as="a"
                                    :href="
                                        route('admin.subjects.edit', subject.id)
                                    "
                                    >تعديل</BaseButton
                                >
                                <BaseButton
                                    class="bg-red-500 text-white hover:bg-red-600"
                                    @click="confirmDelete(subject)"
                                >
                                    حذف
                                </BaseButton>
                            </td>
                        </tr>
                    </tbody>
                </table>
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
        >
            هل أنت متأكد من أنك تريد حذف
            <strong class="text-red-600">{{ subjectToDelete?.title }}</strong>
            ؟ لا يمكن التراجع عن هذا الإجراء.
        </ConfirmDialog>
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
import BaseSelect from '@/components/FormElements/BaseSelect.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, watch, reactive } from 'vue';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    subjects: Object,
    levels: Array,
    teachers: Array,
    courses: Array,
    filters: Object,
});

const showConfirm = ref(false);
const subjectToDelete = ref(null);

const filters = reactive({
    title: props.filters.title || '',
    level_id: props.filters.level_id || '',
    teacher_id: props.filters.teacher_id || '',
    course_id: props.filters.course_id || '',
});

watch(
    filters,
    (newFilters) => {
        router.get(route('admin.subjects.index'), newFilters, {
            preserveState: true,
            replace: true,
        });
    },
    { deep: true },
);

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
