<template>
    <div>
        <Head :title="__('admin.manage_students')" />
        <SectionHeader :title="__('admin.students_list')">
            <template #action>
                <div class="flex gap-2">
                    <BaseButton
                        @click="exportStudents"
                        class="bg-gray-700 hover:bg-gray-800"
                    >
                        {{ __('buttons.export_list') }}
                    </BaseButton>
                    <BaseButton as="a" :href="route('admin.students.create')">{{
                        __('buttons.add_student')
                    }}</BaseButton>
                    <BaseButton
                        as="a"
                        :href="route('admin.students.import.form')"
                        class="bg-green-600 hover:bg-green-700"
                    >
                        {{ __('admin.import_students') }}
                    </BaseButton>
                </div>
            </template>
        </SectionHeader>

        <Card class="mb-4">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <BaseInput
                    v-model="filters.q"
                    :placeholder="__('common.search_name_or_email')"
                />
                <BaseSelect v-model="filters.level_id">
                    <option value="">{{ __('common.all_levels') }}</option>
                    <option
                        v-for="level in levels"
                        :key="level.id"
                        :value="level.id"
                    >
                        {{ level.name }}
                    </option>
                </BaseSelect>
                <BaseSelect v-model="filters.is_approved">
                    <option value="">
                        {{ __('common.any_approval_status') }}
                    </option>
                    <option value="1">{{ __('common.approved') }}</option>
                    <option value="0">{{ __('common.pending') }}</option>
                </BaseSelect>
            </div>
        </Card>

        <Card v-if="students.data.length">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 text-start dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-2">ID</th>
                            <th class="px-4 py-2">{{ __('common.name') }}</th>
                            <th class="px-4 py-2">{{ __('common.email') }}</th>
                            <th class="px-4 py-2">{{ __('common.level') }}</th>
                            <th class="px-4 py-2">
                                {{ __('common.approved') }}
                            </th>
                            <th class="px-4 py-2">
                                {{ __('common.actions') }}
                            </th>
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
                            <td class="px-4 py-2">{{ student.email }}</td>
                            <td class="px-4 py-2">
                                {{ student.level?.name || 'N/A' }}
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
                                            ? __('common.yes')
                                            : __('common.pending')
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
                                    {{ __('common.edit') }}
                                </BaseButton>
                                <BaseButton @click="toggleApproval(student)">{{
                                    student.is_approved
                                        ? __('common.deny')
                                        : __('common.approve')
                                }}</BaseButton>
                                <BaseButton
                                    class="bg-red-500 hover:bg-red-600"
                                    @click="confirmDelete(student)"
                                    >{{ __('common.delete') }}</BaseButton
                                >
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </Card>
        <EmptyState v-else :message="__('admin.no_students_found')" />

        <div class="mt-6">
            <Pagination :links="students.links" />
        </div>

        <ConfirmDialog
            :show="showConfirm"
            :title="__('admin.delete_student')"
            :message="
                __('messages.delete_student_confirm', {
                    name: studentToDelete?.name,
                })
            "
            @confirm="deleteStudent"
            @cancel="showConfirm = false"
        />
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
import { useTranslations } from '@/composables/useTranslations';
import { Head, router } from '@inertiajs/vue3';
import { reactive, ref, watch } from 'vue';

const { __ } = useTranslations();

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
