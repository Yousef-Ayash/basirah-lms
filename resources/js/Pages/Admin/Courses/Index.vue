<script setup>
import AdminLayout from '@/Pages/Admin/Layout.vue';
import Card from '@/components/LayoutStructure/Card.vue';
import BaseButton from '@/components/FormElements/BaseButton.vue';
import BaseInput from '@/components/FormElements/BaseInput.vue';
import Pagination from '@/components/LayoutStructure/Pagination.vue';
import SectionHeader from '@/components/LayoutStructure/SectionHeader.vue';
import ConfirmDialog from '@/components/Misc/ConfirmDialog.vue';
import EmptyState from '@/components/Misc/EmptyState.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    courses: Object,
    filters: Object,
});

const search = ref(props.filters.q || '');
const showConfirm = ref(false);
const courseToDelete = ref(null);

watch(search, (value) => {
    router.get(
        route('admin.courses.index'),
        { q: value },
        {
            preserveState: true,
            replace: true,
        },
    );
});

const confirmDelete = (course) => {
    courseToDelete.value = course;
    showConfirm.value = true;
};

const deleteCourse = () => {
    if (courseToDelete.value) {
        router.delete(route('admin.courses.destroy', courseToDelete.value.id), {
            onFinish: () => {
                showConfirm.value = false;
                courseToDelete.value = null;
            },
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <div>
        <Head title="إدارة المقررات" />
        <SectionHeader title="المقررات التدريبية">
            <template #action>
                <BaseButton as="a" :href="route('admin.courses.create')">
                    + إضافة مقرر جديد
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

        <Card v-if="courses.data.length" class="space-y-2">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 text-right dark:bg-gray-700">
                        <tr>
                            <th class="p-2">الترتيب</th>
                            <th class="p-2">العنوان</th>
                            <th class="p-2">عدد المواد</th>
                            <th class="p-2">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="course in courses.data"
                            :key="course.id"
                            class="border-t border-gray-200 dark:border-gray-700"
                        >
                            <td class="p-2">{{ course.order }}</td>
                            <td class="p-2 font-medium">{{ course.title }}</td>
                            <td class="p-2">
                                {{ course.subjects_count }} مواد
                            </td>
                            <td class="space-x-2 p-2 whitespace-nowrap">
                                <BaseButton
                                    as="a"
                                    :href="
                                        route('admin.courses.edit', course.id)
                                    "
                                    class="bg-blue-500 hover:bg-blue-600"
                                >
                                    تعديل
                                </BaseButton>
                                <BaseButton
                                    class="bg-red-500 hover:bg-red-600"
                                    @click="confirmDelete(course)"
                                >
                                    حذف
                                </BaseButton>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </Card>
        <EmptyState v-else message="لا يوجد مقررات لعرضها." />

        <div class="mt-6">
            <Pagination :links="courses.links" />
        </div>

        <ConfirmDialog
            :show="showConfirm"
            title="حذف المقرر"
            @confirm="deleteCourse"
            @cancel="showConfirm = false"
        >
            هل أنت متأكد من حذف المقرر:
            <strong class="text-red-600">{{ courseToDelete?.title }}</strong
            >؟
            <br />
            سيتم فصل جميع المواد المرتبطة بهذه المقرر.
        </ConfirmDialog>
    </div>
</template>
