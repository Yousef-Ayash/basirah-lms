<script setup>
import AdminLayout from '@/Pages/Admin/Layout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import SectionHeader from '@/components/LayoutStructure/SectionHeader.vue';
import BaseButton from '@/components/FormElements/BaseButton.vue';
import Card from '@/components/LayoutStructure/Card.vue';
import Pagination from '@/components/LayoutStructure/Pagination.vue';
import ConfirmDialog from '@/components/Misc/ConfirmDialog.vue';
import { ref, watch } from 'vue';
import BaseInput from '@/components/FormElements/BaseInput.vue';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    teachers: Object,
    filters: Object,
});

const search = ref(props.filters.q);

watch(search, (value) => {
    router.get(
        route('admin.teachers.index'),
        { q: value },
        {
            preserveState: true,
            replace: true,
        },
    );
});

const q = ref(props.filters.q || '');

const showConfirm = ref(false);
const teacherToDelete = ref(null);

const confirmDelete = (teacher) => {
    teacherToDelete.value = teacher;
    showConfirm.value = true;
};

const deleteTeacher = () => {
    if (teacherToDelete.value) {
        router.delete(
            route('admin.teachers.destroy', teacherToDelete.value.id),
            {
                onFinish: () => {
                    showConfirm.value = false;
                    teacherToDelete.value = null;
                },
                preserveScroll: true,
            },
        );
    }
};
</script>

<template>
    <div>
        <Head title="إدارة المدرسين" />
        <SectionHeader title="المدرسون">
            <template #action>
                <BaseButton as="a" :href="route('admin.teachers.create')"
                    >إضافة مدرس +</BaseButton
                >
            </template>
        </SectionHeader>

        <Card class="mb-4">
            <BaseInput
                v-model="search"
                placeholder="البحث عن طريق الاسم..."
                class="w-full sm:w-2/3"
            />
        </Card>

        <Card>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-100 text-right dark:bg-gray-700">
                            <th class="p-2">صورة</th>
                            <th class="p-2">الاسم</th>
                            <th class="p-2">الإجراءات</th>
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
                                    >تعديل</BaseButton
                                >
                                <BaseButton
                                    @click="confirmDelete(teacher)"
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
        <div class="mt-6">
            <Pagination :links="teachers.links" />
        </div>

        <ConfirmDialog
            :show="showConfirm"
            title="حذف المدرس"
            :message="`هل انت متأكد من حذف المدرس: ${teacherToDelete?.name}`"
            @confirm="deleteTeacher"
            @cancel="showConfirm = false"
        />
    </div>
</template>
