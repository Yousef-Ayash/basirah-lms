<script setup>
import AdminLayout from '@/Pages/Admin/Layout.vue';
import Card from '@/components/LayoutStructure/Card.vue';
import BaseButton from '@/components/FormElements/BaseButton.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import Pagination from '@/components/LayoutStructure/Pagination.vue';
import SectionHeader from '@/components/LayoutStructure/SectionHeader.vue';
import BaseInput from '@/components/FormElements/BaseInput.vue';
import ConfirmDialog from '@/components/Misc/ConfirmDialog.vue';
import EmptyState from '@/components/Misc/EmptyState.vue';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    levels: Object,
    filters: Object,
});

const search = ref(props.filters.q);

watch(search, (value) => {
    router.get(
        route('admin.levels.index'),
        { q: value },
        {
            preserveState: true,
            replace: true,
        },
    );
});

const q = ref(props.filters.q || '');

const showConfirm = ref(false);
const levelToDelete = ref(null);

const confirmDelete = (level) => {
    levelToDelete.value = level;
    showConfirm.value = true;
};

const deleteLevel = () => {
    if (levelToDelete.value) {
        router.delete(route('admin.levels.destroy', levelToDelete.value.id), {
            onFinish: () => {
                showConfirm.value = false;
                levelToDelete.value = null;
            },
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <div>
        <SectionHeader title="المستويات">
            <template #action>
                <BaseButton as="a" :href="route('admin.levels.create')"
                    >إضافة مستوى جديد +</BaseButton
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

        <Card v-if="levels.data.length" class="space-y-2">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 text-right dark:bg-gray-700">
                        <tr>
                            <th class="p-2">ID</th>
                            <th class="p-2">اسم المستوى</th>
                            <th class="p-2">الترتيب</th>
                            <th class="p-2">الاجرائيات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="level in levels.data"
                            :key="level.id"
                            class="border-t"
                        >
                            <td class="p-2">{{ level.id }}</td>
                            <td class="p-2 font-medium">{{ level.name }}</td>
                            <td class="p-2">{{ level.order }}</td>
                            <td class="p-2 whitespace-nowrap">
                                <BaseButton
                                    as="a"
                                    :href="route('admin.levels.edit', level.id)"
                                    class="bg-blue-500 hover:bg-blue-600"
                                    >تعديل</BaseButton
                                >
                                <BaseButton
                                    @click="confirmDelete(level)"
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
        <EmptyState v-else message="لا يوجد مستويات لعرضها." />

        <div class="mt-6">
            <Pagination :links="levels.links" />
        </div>

        <ConfirmDialog
            :show="showConfirm"
            title="حذف المستوى"
            :message="`هل انت متأكد من حذف المستوى: ${levelToDelete?.name}`"
            @confirm="deleteLevel"
            @cancel="showConfirm = false"
        />
    </div>
</template>
