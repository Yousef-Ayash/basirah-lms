<template>
    <Card>
        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-lg font-semibold">
                    {{ attempt.exam?.title ?? 'Attempt' }} — Attempt #{{
                        attempt.id
                    }}
                </h2>
                <div class="text-xs text-gray-500">
                    Started: {{ formatDate(attempt.started_at) }}
                </div>
            </div>

            <div class="text-right">
                <div class="text-sm">
                    Auto score:
                    {{ attempt.score !== null ? attempt.score + '%' : '-' }}
                </div>

                <div v-if="officialMark" class="mt-2 text-green-700">
                    Final mark: <strong>{{ officialMark.marks }}</strong>
                    <div class="text-xs text-gray-500">
                        Source: {{ officialMark.source }}
                    </div>
                </div>

                <div v-else class="mt-2 text-sm text-yellow-600">
                    Final mark: Pending (no official report)
                </div>
            </div>
        </div>

        <TabGroup :tabs="['Answers', 'Mark Details']" v-slot="{ active }">
            <div class="mt-4 space-y-3" v-show="active === 0">
                <div
                    v-for="(ans, idx) in attempt.answers"
                    :key="ans.id"
                    class="rounded border p-3"
                >
                    <div class="font-medium">
                        Q{{ idx + 1 }}.
                        {{ ans.question?.question_text ?? ans.question_text }}
                    </div>
                    <div class="mt-1">
                        Your answer:
                        {{ formatSelected(ans.selected_option) }}
                    </div>
                    <div class="mt-1 text-xs text-gray-500">
                        Correct:
                        {{
                            formatSelected(
                                ans.question?.correct_answer ??
                                    ans.correct_answer,
                            )
                        }}
                    </div>
                    <div
                        class="mt-1"
                        :class="
                            ans.is_correct ? 'text-green-600' : 'text-red-600'
                        "
                    >
                        {{ ans.is_correct ? 'Correct' : 'Wrong' }}
                    </div>
                </div>
            </div>

            <div class="mt-2" v-show="active === 1">
                <div v-if="officialMark">
                    <div class="text-sm">
                        Official mark:
                        <strong>{{ officialMark.marks }}</strong>
                    </div>
                    <div v-if="officialMark.notes" class="mt-2 text-sm">
                        Notes: {{ officialMark.notes }}
                    </div>
                </div>
                <div v-else>
                    <div class="text-sm text-gray-500">
                        No official mark is available yet. Auto-score shown
                        above is provisional.
                    </div>
                </div>
            </div>
        </TabGroup>
    </Card>
</template>

<script>
import { usePage, Head } from '@inertiajs/vue3';
import AdminLayout from '@/Pages/Admin/Layout.vue';
import StudentLayout from '@/Pages/Student/Layout.vue';

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
import TabGroup from '@/components/LayoutStructure/TabGroup.vue';
import { computed } from 'vue';

const props = defineProps({
    attempt: Object,
    // officialMark is optional; server can pass it to avoid client logic
    officialMark: Object,
});

const computedOfficial = computed(() => {
    if (props.officialMark) return props.officialMark;
    // fallback: if attempt has marks_report and it's official, return it
    const mr = props.attempt?.marks_report;
    if (mr && mr.official) {
        return {
            marks: mr.marks,
            notes: mr.notes,
            source: mr.created_by_name ?? mr.creator?.name ?? 'admin',
        };
    }
    return null;
});

function formatDate(iso) {
    return iso ? new Date(iso).toLocaleString() : '-';
}
function formatSelected(v) {
    if (v === null || v === undefined) return '—';
    if (Array.isArray(v)) return v.join(', ');
    return String(v);
}
</script>
