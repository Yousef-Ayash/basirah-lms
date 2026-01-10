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
    admins: Object,
    filters: Object,
});

const search = ref(props.filters.q || '');
const showConfirm = ref(false);
const adminToDelete = ref(null);

watch(search, (value) => {
    router.get(
        route('admin.admins.index'),
        { q: value },
        { preserveState: true, replace: true },
    );
});

const confirmDelete = (admin) => {
    adminToDelete.value = admin;
    showConfirm.value = true;
};

const deleteAdmin = () => {
    if (adminToDelete.value) {
        router.delete(route('admin.admins.destroy', adminToDelete.value.id), {
            onFinish: () => {
                showConfirm.value = false;
                adminToDelete.value = null;
            },
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <div>
        <Head title="إدارة المسؤولين" />
        <SectionHeader title="المسؤولين">
            <template #action>
                <BaseButton as="a" :href="route('admin.admins.create')">
                    + إضافة مسؤول
                </BaseButton>
            </template>
        </SectionHeader>

        <Card class="mb-4">
            <BaseInput
                v-model="search"
                placeholder="البحث بالاسم أو الرقم..."
                class="w-full sm:w-1/2"
            />
        </Card>

        <Card v-if="admins.data.length">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 text-right dark:bg-gray-700">
                        <tr>
                            <th class="p-2">الاسم</th>
                            <th class="p-2">رقم الموبايل</th>
                            <th class="p-2">تاريخ الإضافة</th>
                            <th class="p-2">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="admin in admins.data"
                            :key="admin.id"
                            class="border-t border-gray-200 dark:border-gray-700"
                        >
                            <td class="p-2 font-medium">{{ admin.name }}</td>
                            <td class="p-2">{{ admin.phone }}</td>
                            <td class="p-2">
                                {{
                                    new Date(
                                        admin.created_at,
                                    ).toLocaleDateString()
                                }}
                            </td>
                            <td class="space-x-2 p-2 whitespace-nowrap">
                                <BaseButton
                                    as="a"
                                    :href="route('admin.admins.edit', admin.id)"
                                    class="bg-blue-500 hover:bg-blue-600"
                                >
                                    تعديل
                                </BaseButton>
                                <BaseButton
                                    class="bg-red-500 hover:bg-red-600"
                                    @click="confirmDelete(admin)"
                                    v-if="admin.id !== $page.props.auth.user.id"
                                >
                                    حذف
                                </BaseButton>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </Card>
        <EmptyState v-else message="لم يتم العثور على مسؤولين." />

        <div class="mt-6">
            <Pagination :links="admins.links" />
        </div>

        <ConfirmDialog
            :show="showConfirm"
            title="حذف المسؤول"
            @confirm="deleteAdmin"
            @cancel="showConfirm = false"
        >
            هل أنت متأكد من حذف المسؤول
            <strong class="text-red-600">{{ adminToDelete?.name }}</strong
            >؟
        </ConfirmDialog>
    </div>
</template>
