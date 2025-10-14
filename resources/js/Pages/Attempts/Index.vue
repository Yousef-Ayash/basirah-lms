<template>
    <div>
        <Head title="درجاتي" />
        <SectionHeader title="درجاتي ومحاولتي">
            <template #action>
                <div class="flex gap-2">
                    <BaseButton
                        size="sm"
                        :href="route('attempts.export.xlsx', { page: $page })"
                        as="a"
                        >تصدير الى اكسل</BaseButton
                    >
                    <BaseButton
                        size="sm"
                        :href="route('attempts.export.pdf', { page: $page })"
                        as="a"
                        >تحميل ملف PDF</BaseButton
                    >
                </div>
            </template>
        </SectionHeader>

        <!-- Filters -->
        <Card class="mb-4">
            <div class="flex flex-col justify-between gap-4 md:flex-row">
                <div
                    class="flex flex-col gap-2 md:flex-row md:items-center md:gap-3"
                >
                    <BaseInput
                        label="البحث عن امتحان"
                        v-model="filters.q"
                        placeholder="البحث عن اسم الامتحان..."
                    />
                    <BaseSelect v-model="filters.status" label="الحالة">
                        <option value="">الكل</option>
                        <option value="in_progress">جاري التقديم</option>
                        <option value="pending">تم التسليم (بالانتظار)</option>
                        <option value="passed">ناحج</option>
                        <option value="failed">راسب</option>
                    </BaseSelect>
                    <BaseInput
                        type="date"
                        label="من تاريخ"
                        v-model="filters.from"
                    />
                    <BaseInput
                        type="date"
                        label="إلى تاريخ"
                        v-model="filters.to"
                    />
                    <BaseSelect
                        label="العدد بالصفحة"
                        v-model="filters.per_page"
                    >
                        <option :value="10">10</option>
                        <option :value="15">15</option>
                        <option :value="25">25</option>
                        <option :value="50">50</option>
                    </BaseSelect>
                </div>
                <div class="flex items-center justify-center gap-2">
                    <BaseButton @click="clearFilters" class="w-full"
                        >مسح الفلاتر</BaseButton
                    >
                </div>
            </div>
        </Card>

        <Card>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 text-right dark:bg-gray-700">
                        <tr>
                            <th class="p-2">الامتحان</th>
                            <th class="p-2">وقت البدء</th>
                            <th class="p-2">وقت التسليم</th>
                            <th class="p-2">النسبة</th>
                            <th class="p-2">الدرجة</th>
                            <th class="p-2">الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="attempt in attempts.data"
                            :key="attempt.id"
                            class="border-t dark:border-gray-700"
                        >
                            <td class="p-2">
                                {{ attempt.exam?.title ?? '—' }}
                            </td>
                            <td class="p-2">{{ fmt(attempt.started_at) }}</td>
                            <td class="p-2">
                                {{
                                    attempt.submitted_at
                                        ? fmt(attempt.submitted_at)
                                        : '-'
                                }}
                            </td>
                            <td class="p-2">
                                <div
                                    v-if="
                                        attempt.marks_report &&
                                        attempt.marks_report.official
                                    "
                                >
                                    <strong
                                        >{{
                                            attempt.marks_report.score
                                        }}%</strong
                                    >
                                </div>
                                <div v-else-if="attempt.mark !== null">
                                    {{ attempt.score }} %
                                    <span class="text-xs text-gray-400"
                                        >({{ attempt.mark ?? '-' }})</span
                                    >
                                </div>
                                <div v-else class="text-gray-500">
                                    قيد الانتظار
                                </div>
                            </td>
                            <td class="p-2">
                                <div
                                    v-if="
                                        attempt.marks_report &&
                                        attempt.marks_report.official
                                    "
                                >
                                    <strong>{{
                                        attempt.marks_report.marks
                                    }}</strong>
                                </div>
                                <div v-else-if="attempt.mark !== null">
                                    {{ attempt.mark }}
                                    <span class="text-xs text-gray-400"
                                        >({{ attempt.score ?? '-' }}%)</span
                                    >
                                </div>
                                <div v-else class="text-gray-500">
                                    قيد الانتظار
                                </div>
                            </td>
                            <td class="p-2">
                                <span
                                    :class="
                                        statusClass(statusForAttempt(attempt))
                                    "
                                    >{{ statusForAttempt(attempt) }}</span
                                >
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <Pagination :links="attempts.links" :meta="attempts.meta" />
        </Card>
    </div>
</template>

<script>
import { usePage, Head } from '@inertiajs/vue3';
import AdminLayout from '@/Pages/Admin/Layout.vue';
import StudentLayout from '@/Pages/Student/Layout.vue';
import BaseInput from '@/components/FormElements/BaseInput.vue';
import { attempt } from 'lodash';

export default {
    layout: (h, page) => {
        const user = usePage().props.auth.user;
        const isAdmin = user.roles?.some((role) => role.name === 'admin');
        const LayoutComponent = isAdmin ? AdminLayout : StudentLayout;
        return h(LayoutComponent, [page]);
    },
};
</script>

<script setup>
import Card from '@/components/LayoutStructure/Card.vue';
import BaseButton from '@/components/FormElements/BaseButton.vue';
import Pagination from '@/components/LayoutStructure/Pagination.vue';
import { reactive, watch } from 'vue';
import BaseInput from '@/components/FormElements/BaseInput.vue';
import BaseSelect from '@/components/FormElements/BaseSelect.vue';
import { Head, router } from '@inertiajs/vue3';
import SectionHeader from '@/components/LayoutStructure/SectionHeader.vue';

const props = defineProps({ attempts: Object, filters: Object });

const filters = reactive({
    q: props.filters?.q ?? '',
    status: props.filters?.status ?? '',
    from: props.filters?.from ?? '',
    to: props.filters?.to ?? '',
    per_page: props.filters?.per_page ?? 15,
});

watch(
    filters,
    (newFilters) => {
        router.get(route('attempts.index'), newFilters, {
            preserveState: true,
            replace: true,
        });
    },
    { deep: true },
);

function fmt(iso) {
    return iso ? new Date(iso).toLocaleString() : '-';
}

function statusForAttempt(a) {
    if (!a.submitted_at) return 'In Progress';
    if (a.marks_report && a.marks_report.official) {
        const threshold = a.exam?.pass_threshold ?? a.exam?.pass_mark ?? 50;
        return a.marks_report.marks >= threshold ? 'Passed' : 'Failed';
    }
    if (a.score !== null && a.score !== undefined) {
        const threshold = a.exam?.pass_threshold ?? a.exam?.pass_mark ?? 50;
        return a.score >= threshold ? 'Passed' : 'Failed';
    }
    return 'Submitted (Pending)';
}

function statusClass(s) {
    if (s === 'Passed') return 'text-green-600';
    if (s === 'Failed') return 'text-red-600';
    if (s === 'In Progress') return 'text-gray-600';
    return 'text-yellow-600';
}

/* Clear filters: reset local and fetch base index */
function clearFilters() {
    filters.q = '';
    filters.status = '';
    filters.from = '';
    filters.to = '';
    filters.per_page = 15;
    router.get(
        route('attempts.index'),
        {},
        { preserveState: true, replace: true },
    );
}
</script>
