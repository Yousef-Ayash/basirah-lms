<script setup>
import AdminLayout from '@/Pages/Admin/Layout.vue';
import BaseButton from '@/components/FormElements/BaseButton.vue';
import BaseInput from '@/components/FormElements/BaseInput.vue';
import BaseSelect from '@/components/FormElements/BaseSelect.vue';
import Card from '@/components/LayoutStructure/Card.vue';
import Pagination from '@/components/LayoutStructure/Pagination.vue';
// import SectionHeader from '@/components/LayoutStructure/SectionHeader.vue';
import ConfirmDialog from '@/components/Misc/ConfirmDialog.vue';
import EmptyState from '@/components/Misc/EmptyState.vue';
import { Head, router } from '@inertiajs/vue3';
import { reactive, ref, watch } from 'vue';
import PageHeader from '@/components/LayoutStructure/PageHeader.vue';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    marks: Object,
    users: Array,
    exams: Array,
    filters: Object,
});

const filters = reactive({
    user_id: props.filters.user_id || '',
    exam_id: props.filters.exam_id || '',
    date_from: props.filters.date_from || '',
    date_to: props.filters.date_to || '',
});

watch(
    filters,
    (newFilters) => {
        router.get(route('admin.marks.index'), newFilters, {
            preserveState: true,
            replace: true,
        });
    },
    { deep: true },
);

// --- 2. Add state for the confirmation dialog ---
const showConfirm = ref(false);
const markToDelete = ref(null);

const confirmDelete = (mark) => {
    markToDelete.value = mark;
    showConfirm.value = true;
};

const deleteMark = () => {
    if (markToDelete.value) {
        router.delete(route('admin.marks.destroy', markToDelete.value.id), {
            onFinish: () => {
                showConfirm.value = false;
                markToDelete.value = null;
            },
            preserveScroll: true,
        });
    }
};

const exportMarks = () => {
    // Start with the base URL for the export route
    let url = route('admin.marks.export');

    // Create a clean set of filters, removing any empty values
    const activeFilters = {};
    for (const key in filters) {
        if (filters[key]) {
            activeFilters[key] = filters[key];
        }
    }

    // If there are active filters, append them as a query string
    if (Object.keys(activeFilters).length > 0) {
        const query = new URLSearchParams(activeFilters).toString();
        url = `${url}?${query}`;
    }

    // Navigate to the URL; the browser will handle the file download
    window.location.href = url;
};
</script>

<template>
    <div>
        <!-- <Head title="إدارة الدرجات" />
        <SectionHeader title="إدارة الدرجات">
            <template #action>
                <div class="flex gap-2">
                    <BaseButton
                        @click="exportMarks"
                        class="bg-gray-700 hover:bg-gray-800"
                    >
                        تصدير الدرجات
                    </BaseButton>
                    <BaseButton
                        as="a"
                        :href="route('admin.marks.import.form')"
                        class="bg-green-600 hover:bg-green-700"
                    >
                        استيراد الدرجات
                    </BaseButton>
                    <BaseButton as="a" :href="route('admin.marks.create')">
                        + إضافة درجة يدوياً
                    </BaseButton>
                </div>
            </template>
        </SectionHeader> -->

        <PageHeader title="إدارة الدرجات">
            <template #actions>
                <div class="flex gap-2">
                    <BaseButton
                        @click="exportMarks"
                        class="bg-gray-700 hover:bg-gray-800"
                        >تصدير الدرجات</BaseButton
                    >
                    <BaseButton
                        as="a"
                        :href="route('admin.marks.import.form')"
                        class="bg-green-600 hover:bg-green-700"
                        >استيراد الدرجات</BaseButton
                    >
                    <BaseButton as="a" :href="route('admin.marks.create')"
                        >+ إضافة درجة يدوياً</BaseButton
                    >
                </div>
            </template>
            <template #filters>
                <div
                    class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4"
                >
                    <BaseSelect
                        v-model="filters.user_id"
                        label="تصفية حسب الطالب"
                    >
                        <option value="">تصفية حسب الطالب...</option>
                        <option
                            v-for="user in users"
                            :key="user.id"
                            :value="user.id"
                        >
                            {{ user.name }}
                        </option>
                    </BaseSelect>
                    <BaseSelect
                        v-model="filters.exam_id"
                        label="تصفية حسب الاختبار"
                    >
                        <option value="" disabled>تصفية حسب الاختبار...</option>
                        <option
                            v-for="exam in exams"
                            :key="exam.id"
                            :value="exam.id"
                        >
                            {{ exam.title }}
                        </option>
                    </BaseSelect>
                    <BaseInput
                        type="date"
                        label="من تاريخ"
                        v-model="filters.date_from"
                    />
                    <BaseInput
                        type="date"
                        label="إلى تاريخ"
                        v-model="filters.date_to"
                    />
                </div>
            </template>
        </PageHeader>

        <Card class="mb-4">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <BaseSelect v-model="filters.user_id" label="تصفية حسب الطالب">
                    <option value="">تصفية حسب الطالب...</option>
                    <option
                        v-for="user in users"
                        :key="user.id"
                        :value="user.id"
                    >
                        {{ user.name }}
                    </option>
                </BaseSelect>
                <BaseSelect
                    v-model="filters.exam_id"
                    label="تصفية حسب الاختبار"
                >
                    <option value="" disabled>تصفية حسب الاختبار...</option>
                    <option
                        v-for="exam in exams"
                        :key="exam.id"
                        :value="exam.id"
                    >
                        {{ exam.title }}
                    </option>
                </BaseSelect>
                <BaseInput
                    type="date"
                    label="من تاريخ"
                    v-model="filters.date_from"
                />
                <BaseInput
                    type="date"
                    label="إلى تاريخ"
                    v-model="filters.date_to"
                />
            </div>
        </Card>

        <Card v-if="marks.data.length" class="space-y-2">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 text-right dark:bg-gray-700">
                        <tr>
                            <th class="p-2">الطالب</th>
                            <th class="p-2">الاختبار</th>
                            <th class="p-2">المادة</th>
                            <th class="p-2">الدرجات</th>
                            <th class="p-2">أضيف بواسطة</th>
                            <th class="p-2">عُدل بواسطة</th>
                            <th class="p-2">التاريخ</th>
                            <th class="p-2">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="mark in marks.data"
                            :key="mark.id"
                            class="border-t dark:border-gray-700"
                        >
                            <td class="p-2">{{ mark.user.name }}</td>
                            <td class="p-2">{{ mark.exam.title }}</td>
                            <td class="p-2">{{ mark.exam.subject.title }}</td>
                            <td class="p-2 font-bold">{{ mark.marks }}</td>
                            <td class="p-2">
                                {{
                                    mark.creator
                                        ? mark.creator.name
                                        : 'النظام الامتحاني'
                                }}
                            </td>
                            <td class="p-2">
                                {{
                                    mark.updater
                                        ? mark.updater.name
                                        : 'النظام الامتحاني'
                                }}
                            </td>
                            <td class="p-2">
                                {{
                                    new Date(
                                        mark.created_at,
                                    ).toLocaleDateString()
                                }}
                            </td>
                            <td class="p-2 whitespace-nowrap">
                                <BaseButton
                                    as="a"
                                    :href="route('admin.marks.edit', mark.id)"
                                    class="bg-blue-500 hover:bg-blue-600"
                                    >تعديل</BaseButton
                                >
                                <BaseButton
                                    @click="confirmDelete(mark)"
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
        <EmptyState v-else message="لا يوجد أي علامات لعرضها." />

        <div class="mt-6">
            <Pagination :links="marks.links" />
        </div>

        <ConfirmDialog
            :show="showConfirm"
            title="حذف العلامة"
            @confirm="deleteMark"
            @cancel="showConfirm = false"
        >
            هل أنت متأكد من حذف علامة الاختبار
            <strong class="text-red-600">{{ markToDelete?.exam.title }}</strong>
            للمستخدم
            <strong class="text-red-600">{{ markToDelete?.user.name }}</strong
            >؟ لا يمكن التراجع عن هذا الإجراء.
        </ConfirmDialog>
    </div>
</template>
