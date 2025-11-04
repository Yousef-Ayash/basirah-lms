<template>
    <div>
        <Head :title="`المحاولات — ${exam.title}`" />

        <SectionHeader
            :title="`المحاولات — ${exam.title}`"
            :info="`العدد الكلي: ${attempts.meta?.total ?? attempts.data.length}`"
        />

        <Card v-if="attempts.data.length" class="space-y-2">
            <div class="overflow-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 text-right dark:bg-gray-700">
                        <tr>
                            <th class="p-2">#</th>
                            <th class="p-2">الطالب</th>
                            <th class="p-2">وقت البدء</th>
                            <th class="p-2">وقت التسليم</th>
                            <th class="p-2">الدرجة</th>
                            <th class="p-2">الحالة</th>
                            <th class="p-2">الاجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="attempt in attempts.data"
                            :key="attempt.id"
                            class="border-t dark:border-gray-700"
                        >
                            <td class="p-2">{{ attempt.id }}</td>
                            <td class="p-2">
                                <div class="font-medium">
                                    {{ attempt.user?.name ?? '—' }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    <!-- {{ attempt.user?.email ?? '' }} -->
                                    {{ attempt.user?.phone ?? '' }}
                                </div>
                            </td>
                            <td class="p-2">
                                {{ formatDate(attempt.started_at) }}
                            </td>
                            <td class="p-2">
                                {{
                                    attempt.submitted_at
                                        ? formatDate(attempt.submitted_at)
                                        : '-'
                                }}
                            </td>
                            <td class="p-2">
                                <div
                                    v-if="
                                        attempt.marks_report &&
                                        attempt.marks_report.marks !== null
                                    "
                                >
                                    {{ attempt.marks_report.marks }}
                                    <div class="text-xs text-gray-400">
                                        ({{
                                            attempt.marks_report.updater
                                                ?.name ??
                                            attempt.marks_report.creator
                                                ?.name ??
                                            'النظام الامتحاني'
                                        }})
                                    </div>
                                </div>
                                <div v-else-if="attempt.mark !== null">
                                    {{ attempt.mark }}
                                    <span class="text-xs text-gray-400"
                                        >({{ attempt.score ?? '-' }}%)</span
                                    >
                                </div>
                                <div v-else class="text-gray-400">—</div>
                            </td>

                            <td class="p-2">
                                <span
                                    :class="
                                        statusClass(statusForAttempt(attempt))
                                    "
                                    >{{ statusForAttempt(attempt) }}</span
                                >
                            </td>

                            <td class="p-2 whitespace-nowrap">
                                <Link
                                    :href="
                                        route('admin.exams.attempts.show', {
                                            exam: exam.id,
                                            attempt: attempt.id,
                                        })
                                    "
                                >
                                    <BaseButton size="sm">عرض</BaseButton>
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- <Pagination :links="attempts.links" :meta="attempts.meta" /> -->
        </Card>
        <EmptyState v-else message="لا يوجد أي محاولات لعرضها." />

        <div class="mt-6">
            <Pagination :links="attempts.links" :meta="attempts.meta" />
        </div>
    </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import { Inertia } from '@inertiajs/inertia';
import Card from '@/components/LayoutStructure/Card.vue';
import BaseButton from '@/components/FormElements/BaseButton.vue';
import Pagination from '@/components/LayoutStructure/Pagination.vue';
import AdminLayout from '@/Pages/Admin/Layout.vue';
import SectionHeader from '@/components/LayoutStructure/SectionHeader.vue';
import EmptyState from '@/components/Misc/EmptyState.vue';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    exam: Object,
    attempts: Object,
});

function formatDate(iso) {
    return iso ? new Date(iso).toLocaleString() : '-';
}
function refresh() {
    Inertia.get(window.location.href);
}

function statusForAttempt(a) {
    if (!a.submitted_at) return 'In Progress';
    // official marks_report first
    if (a.marks_report /*&& a.marks_report.official*/) {
        const threshold = a.exam?.pass_threshold ?? a.exam?.pass_mark ?? 50;
        return a.marks_report.marks >= threshold ? 'Passed' : 'Failed';
    }
    // fallback to attempt score/mark
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
</script>
