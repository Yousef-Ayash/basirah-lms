<template>
    <div>
        <Head title="إدارة الطلاب" />
        <SectionHeader title="قائمة الطلاب">
            <template #action>
                <div class="flex gap-2">
                    <BaseButton
                        @click="exportStudents"
                        class="bg-gray-700 hover:bg-gray-800"
                    >
                        تصدير القائمة
                    </BaseButton>
                    <BaseButton as="a" :href="route('admin.students.create')"
                        >+ إضافة طالب</BaseButton
                    >
                    <BaseButton
                        as="a"
                        :href="route('admin.students.import.form')"
                        class="bg-green-600 hover:bg-green-700"
                    >
                        استيراد الطلاب
                    </BaseButton>
                </div>
            </template>
        </SectionHeader>

        <Card class="mb-4">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <BaseInput
                    v-model="filters.q"
                    placeholder="البحث بالاسم أو البريد الإلكتروني..."
                />
                <BaseSelect v-model="filters.level_id">
                    <option value="">كل المستويات</option>
                    <option
                        v-for="level in levels"
                        :key="level.id"
                        :value="level.id"
                    >
                        {{ level.name }}
                    </option>
                </BaseSelect>
                <BaseSelect v-model="filters.is_approved">
                    <option value="">أي حالة موافقة</option>
                    <option value="1">تمت الموافقة</option>
                    <option value="0">قيد الانتظار</option>
                </BaseSelect>
            </div>
        </Card>

        <Card v-if="students.data.length">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 text-right dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-2">ID</th>
                            <th class="px-4 py-2">الاسم</th>
                            <!-- <th class="px-4 py-2">البريد الإلكتروني</th> -->
                            <th class="px-4 py-2">رقم الموبايل</th>
                            <th class="px-4 py-2">المستوى</th>
                            <th class="px-4 py-2">تمت الموافقة</th>
                            <th class="px-4 py-2">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="student in students.data"
                            :key="student.id"
                            class="border-t border-gray-200 dark:border-gray-700"
                        >
                            <td class="px-4 py-2 whitespace-nowrap">
                                {{ student.id }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap">
                                {{ student.name }}
                            </td>
                            <!-- <td class="px-4 py-2">{{ student.email }}</td> -->
                            <td class="px-4 py-2">{{ student.phone }}</td>
                            <td class="px-4 py-2">
                                {{ student.level?.name || 'بلا مستوى' }}
                            </td>
                            <td class="px-4 py-2">
                                <span
                                    :class="
                                        student.is_approved
                                            ? 'text-green-500'
                                            : 'text-yellow-500'
                                    "
                                >
                                    {{
                                        student.is_approved
                                            ? 'نعم'
                                            : 'قيد الانتظار'
                                    }}
                                </span>
                            </td>
                            <td class="space-x-2 px-4 py-2 whitespace-nowrap">
                                <BaseButton
                                    as="a"
                                    :href="
                                        route('admin.students.edit', {
                                            student: student.id,
                                        })
                                    "
                                    class="bg-blue-500 hover:bg-blue-600"
                                >
                                    تعديل
                                </BaseButton>
                                <BaseButton @click="toggleApproval(student)">{{
                                    student.is_approved ? 'رفض' : 'موافقة'
                                }}</BaseButton>
                                <BaseButton
                                    class="bg-red-500 hover:bg-red-600"
                                    @click="confirmDelete(student)"
                                    >حذف</BaseButton
                                >
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </Card>
        <EmptyState v-else message="لم يتم العثور على طلاب" />

        <div class="mt-6">
            <Pagination :links="students.links" />
        </div>

        <ConfirmDialog
            :show="showConfirm"
            title="حذف الطالب"
            @confirm="deleteStudent"
            @cancel="showConfirm = false"
        >
            هل أنت متأكد من حذف الطالب
            <strong class="text-red-600">{{ studentToDelete?.name }}</strong
            >؟ لا يمكن التراجع عن هذا الإجراء.
        </ConfirmDialog>
    </div>
</template>

<script setup>
import AdminLayout from '@/Pages/Admin/Layout.vue';
import BaseButton from '@/components/FormElements/BaseButton.vue';
import BaseInput from '@/components/FormElements/BaseInput.vue';
import BaseSelect from '@/components/FormElements/BaseSelect.vue';
import Card from '@/components/LayoutStructure/Card.vue';
import Pagination from '@/components/LayoutStructure/Pagination.vue';
import SectionHeader from '@/components/LayoutStructure/SectionHeader.vue';
import ConfirmDialog from '@/components/Misc/ConfirmDialog.vue';
import EmptyState from '@/components/Misc/EmptyState.vue';

import { Head, router } from '@inertiajs/vue3';
import { reactive, ref, watch } from 'vue';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    students: Object,
    levels: Array,
    filters: Object,
});

const showConfirm = ref(false);
const studentToDelete = ref(null);

const filters = reactive({
    q: props.filters.q || '',
    level_id: props.filters.level_id || '',
    is_approved: props.filters.is_approved || '',
});

watch(
    filters,
    (newFilters) => {
        router.get(route('admin.students.index'), newFilters, {
            preserveState: true,
            replace: true,
        });
    },
    { deep: true },
);

const toggleApproval = (student) => {
    const action = student.is_approved ? 'deny' : 'approve';
    router.post(
        route(`admin.users.${action}`, { user: student.id }),
        {},
        { preserveScroll: true },
    );
};

const confirmDelete = (student) => {
    studentToDelete.value = student;
    showConfirm.value = true;
};

const deleteStudent = () => {
    if (studentToDelete.value) {
        router.delete(
            route('admin.students.destroy', {
                student: studentToDelete.value.id,
            }),
            {
                onFinish: () => {
                    showConfirm.value = false;
                    studentToDelete.value = null;
                },
                preserveScroll: true,
            },
        );
    }
};

const exportStudents = () => {
    let url = route('admin.students.export');

    const activeFilters = {};
    for (const key in filters) {
        if (filters[key]) {
            activeFilters[key] = filters[key];
        }
    }

    if (Object.keys(activeFilters).length > 0) {
        const query = new URLSearchParams(activeFilters).toString();
        url = `${url}?${query}`;
    }

    window.location.href = url;
};
</script>
