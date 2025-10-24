<template>
    <div>
        <Head :title="`Review Attempt #${props.attempt.id}`" />

        <Card class="mb-6 space-y-4">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-xl font-semibold">
                        المحاولة #{{ props.attempt.id }} —
                        {{ props.exam.title }}
                    </h2>
                    <p class="text-sm text-gray-500">
                        Student:
                        <span class="font-medium">{{
                            props.attempt.user?.name
                        }}</span>
                        <span class="ml-2 text-xs text-gray-400">
                            <!-- ({{ props.attempt.user?.email }}) -->
                            ({{ props.attempt.user?.phone }})
                        </span>
                    </p>
                    <div class="mt-2 text-sm text-gray-600">
                        <span class="mr-3"
                            >Started: {{ fmt(props.attempt.started_at) }}</span
                        >
                        <span
                            >Submitted:
                            {{
                                props.attempt.submitted_at
                                    ? fmt(props.attempt.submitted_at)
                                    : '—'
                            }}</span
                        >
                    </div>
                </div>

                <div class="text-right">
                    <div class="text-sm">
                        Total mark:
                        <strong class="text-lg">{{
                            displayMark(props.attempt)
                        }}</strong>
                    </div>
                    <div class="mt-1 text-xs text-gray-500">
                        Score:
                        <strong>{{ props.attempt.score ?? '-' }}%</strong>
                    </div>
                    <div class="mt-3">
                        <span
                            :class="
                                statusClass(statusForAttempt(props.attempt))
                            "
                            class="rounded px-2 py-1 text-sm"
                        >
                            {{ statusForAttempt(props.attempt) }}
                        </span>
                    </div>

                    <div class="mt-3 flex justify-end gap-2">
                        <Link
                            v-if="props.attempt.marks_report"
                            :href="
                                route(
                                    'admin.marks.edit',
                                    props.attempt.marks_report.id,
                                )
                            "
                        >
                            <BaseButton size="sm">Edit Mark</BaseButton>
                        </Link>
                    </div>
                </div>
            </div>
        </Card>

        <Card class="space-y-4">
            <TabGroup :tabs="['Answers', 'Logs']" v-slot="{ active }">
                <!-- ANSWERS PANEL -->
                <div v-show="active === 0" class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-600">
                            Showing
                            {{ props.attempt.answers?.length ?? 0 }} question(s)
                        </div>
                        <div class="text-sm text-gray-500">
                            Total possible: <strong>{{ totalPossible }}</strong>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <Card
                            v-for="(ans, idx) in props.attempt.answers || []"
                            :key="ans.id ?? idx"
                            class="overflow-hidden rounded-lg border shadow-sm"
                        >
                            <div class="p-4 md:p-5">
                                <div class="flex items-start justify-between">
                                    <div class="pr-4">
                                        <div class="flex items-baseline gap-3">
                                            <div class="mb-4 font-semibold">
                                                Q{{ idx + 1 }}:
                                            </div>
                                            <div
                                                class="leading-snug font-medium"
                                            >
                                                {{
                                                    ans.question
                                                        ?.question_text ??
                                                    ans.question_text
                                                }}
                                            </div>
                                        </div>

                                        <div class="mt-2 text-xs text-gray-500">
                                            <span v-if="ans.question?.mark"
                                                >Points:
                                                {{ ans.question.mark }}</span
                                            >
                                            <span v-else>Points: 1</span>
                                            <span class="mx-2">•</span>
                                            <span v-if="ans.time_spent"
                                                >Time:
                                                {{
                                                    formatSeconds(
                                                        ans.time_spent,
                                                    )
                                                }}</span
                                            >
                                        </div>
                                    </div>

                                    <div class="text-right">
                                        <div
                                            class="inline-flex items-center gap-2"
                                        >
                                            <template v-if="ans.is_correct">
                                                <svg
                                                    class="h-5 w-5 text-green-600"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1 1 0 10-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1 1 0 000-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                                <span
                                                    class="text-sm font-semibold text-green-700"
                                                    >{{
                                                        ans.score_awarded ?? 0
                                                    }}</span
                                                >
                                            </template>
                                            <template v-else>
                                                <svg
                                                    class="h-5 w-5 text-red-600"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11V5a1 1 0 10-2 0v2a1 1 0 002 0zm-1 4a1 1 0 100 2 1 1 0 000-2z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                                <span
                                                    class="text-sm font-semibold text-red-700"
                                                    >{{
                                                        ans.score_awarded ?? 0
                                                    }}</span
                                                >
                                            </template>
                                        </div>
                                        <div class="mt-1 text-xs text-gray-400">
                                            Awarded
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 grid gap-2">
                                    <template
                                        v-if="
                                            Array.isArray(
                                                ans.question?.options,
                                            ) && ans.question.options.length > 0
                                        "
                                    >
                                        <div
                                            v-for="(opt, i) in ans.question
                                                .options"
                                            :key="i"
                                            class="flex items-center space-x-3 rounded-lg border p-3 dark:border-gray-700"
                                            :class="optionClass(opt, ans)"
                                        >
                                            <div class="w-full">
                                                <div
                                                    class="flex items-center justify-between"
                                                >
                                                    <div
                                                        class="text-sm"
                                                        :class="{
                                                            'dark:text-[#1e2939]':
                                                                isSelected(
                                                                    opt,
                                                                    ans,
                                                                ) ||
                                                                isCorrectOption(
                                                                    opt,
                                                                    ans,
                                                                ),
                                                        }"
                                                    >
                                                        {{ renderOption(opt) }}
                                                    </div>
                                                    <div
                                                        class="text-xs font-bold"
                                                        :class="{
                                                            'dark:text-[#1e2939]':
                                                                isSelected(
                                                                    opt,
                                                                    ans,
                                                                ) ||
                                                                isCorrectOption(
                                                                    opt,
                                                                    ans,
                                                                ),
                                                        }"
                                                        v-if="
                                                            isSelected(
                                                                opt,
                                                                ans,
                                                            ) ||
                                                            isCorrectOption(
                                                                opt,
                                                                ans,
                                                            )
                                                        "
                                                    >
                                                        <span
                                                            v-if="
                                                                isSelected(
                                                                    opt,
                                                                    ans,
                                                                )
                                                            "
                                                            >Selected</span
                                                        >
                                                        <span
                                                            v-else-if="
                                                                isCorrectOption(
                                                                    opt,
                                                                    ans,
                                                                )
                                                            "
                                                            >Correct</span
                                                        >
                                                    </div>
                                                </div>
                                                <div
                                                    v-if="opt.explanation"
                                                    class="mt-2 text-xs text-gray-500"
                                                >
                                                    {{ opt.explanation }}
                                                </div>
                                            </div>
                                        </div>
                                    </template>

                                    <template v-else>
                                        <div class="text-sm text-gray-700">
                                            <div>
                                                <span
                                                    class="text-xs text-gray-500"
                                                    >Student answer:</span
                                                >
                                                {{
                                                    fmtSelected(
                                                        ans.selected_option,
                                                    )
                                                }}
                                            </div>
                                            <div class="mt-1">
                                                <span
                                                    class="text-xs text-gray-500"
                                                    >Correct answer:</span
                                                >
                                                {{
                                                    fmtSelected(
                                                        ans.question
                                                            ?.correct_answer ??
                                                            ans.correct_answer,
                                                    )
                                                }}
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                <div
                                    v-if="
                                        ans.notes || ans.question?.explanation
                                    "
                                    class="mt-3 text-sm text-gray-700"
                                >
                                    <div v-if="ans.notes">
                                        <strong>Note:</strong> {{ ans.notes }}
                                    </div>
                                    <div
                                        v-if="ans.question?.explanation"
                                        class="mt-1"
                                    >
                                        <strong>Question note:</strong>
                                        {{ ans.question.explanation }}
                                    </div>
                                </div>
                            </div>
                        </Card>
                    </div>
                </div>

                <!-- LOGS PANEL -->
                <div v-show="active === 1" class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-600">
                            Activity log ({{ attempt.logs?.length ?? 0 }})
                        </div>
                        <div class="text-sm text-gray-500">Latest first</div>
                    </div>

                    <div class="space-y-3">
                        <div
                            v-for="log in (attempt.logs || [])
                                .slice()
                                .reverse()"
                            :key="log.id"
                            class="rounded-md border p-3 shadow-sm"
                        >
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        :class="
                                            logTagClass(log.event ?? log.action)
                                        "
                                    >
                                        {{ logLabel(log.event ?? log.action) }}
                                    </div>
                                    <div>
                                        <div class="font-medium">
                                            {{
                                                log.actor?.name ??
                                                log.user?.name ??
                                                'System'
                                            }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{
                                                logLabel(
                                                    log.event ?? log.action,
                                                )
                                            }}
                                            · {{ fmt(log.created_at) }}
                                            <span class="mx-1">·</span>
                                            <span
                                                class="text-xs text-gray-400"
                                                >{{
                                                    relativeTime(log.created_at)
                                                }}</span
                                            >
                                        </div>
                                    </div>
                                </div>

                                <div class="text-right text-xs text-gray-500">
                                    <div v-if="log.ip">IP: {{ log.ip }}</div>
                                    <div v-if="log.user_agent">
                                        UA: {{ shortUA(log.user_agent) }}
                                    </div>
                                </div>
                            </div>

                            <div class="mt-2 text-sm text-gray-700">
                                <div v-if="log.meta || log.metadata">
                                    <details class="mt-1">
                                        <summary
                                            class="cursor-pointer text-xs text-blue-600"
                                        >
                                            View metadata
                                        </summary>
                                        <pre
                                            class="mt-2 overflow-auto rounded bg-gray-50 p-3 text-xs"
                                            >{{
                                                prettyMeta(
                                                    log.meta ?? log.metadata,
                                                )
                                            }}</pre
                                        >
                                    </details>
                                </div>
                                <div v-else class="text-xs text-gray-500">
                                    No extra data.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </TabGroup>

            <div class="flex justify-end">
                <Link
                    :href="
                        route('admin.exams.attempts.index', {
                            exam: attempt.exam_id,
                        })
                    "
                >
                    <BaseButton size="sm">Back</BaseButton>
                </Link>
            </div>
        </Card>
    </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Pages/Admin/Layout.vue';
import Card from '@/components/LayoutStructure/Card.vue';
import TabGroup from '@/components/LayoutStructure/TabGroup.vue';
import BaseButton from '@/components/FormElements/BaseButton.vue';
import { computed } from 'vue';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    attempt: Object,
    exam: Object,
});

/*
    Notes / server-side suggestions
    - attempt.answers should include:
        - id, question_id, selected_option, is_correct, score_awarded, notes, time_spent (optional)
        - question relation with: question_text, options (array of { value, label, explanation? }), correct_answer, mark, explanation
    - attempt.logs items should include:
        - id, event/action, created_at, meta/metadata (JSON), ip, user_agent, actor/user relation (name,email)
    - marks_report (if exists) is available on attempt.marks_report
  */

const totalPossible = computed(() => {
    if (!props.attempt.answers || props.attempt.answers.length === 0) return 0;
    return props.attempt.answers.reduce((sum, a) => {
        const m = a.question?.mark ?? 1;
        return sum + (typeof m === 'number' ? m : Number(m || 1));
    }, 0);
});

function fmt(iso) {
    return iso ? new Date(iso).toLocaleString() : '-';
}

function fmtSelected(v) {
    if (v === null || v === undefined) return '—';
    if (Array.isArray(v)) return v.join(', ');
    return String(v);
}

function formatSeconds(s) {
    if (!s && s !== 0) return '—';
    const sec = Number(s);
    const m = Math.floor(sec / 60);
    const r = sec % 60;
    return m > 0 ? `${m}m ${r}s` : `${r}s`;
}

function displayMark(a) {
    if (a.marks_report && a.marks_report.marks !== null)
        return a.marks_report.marks;
    if (a.mark !== null && a.mark !== undefined)
        return `${a.mark} (${a.score ?? '-'}%)`;
    return '-';
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
    if (s === 'Passed') return 'bg-green-50 text-green-700';
    if (s === 'Failed') return 'bg-red-50 text-red-700';
    if (s === 'In Progress') return 'bg-gray-50 text-gray-700';
    return 'bg-yellow-50 text-yellow-800';
}

/* OPTIONS helpers: highlight selected & correct */
/* ---------- Normalizers & helpers (robust) ---------- */

/** Return the "value" of an option item (primitive or object) as a string. */
function getOptionValue(opt) {
    if (opt === null || opt === undefined) return String(opt);
    if (typeof opt === 'object') {
        // prefer explicit value, fallback to id/key/label or JSON string
        return String(
            opt.value ?? opt.id ?? opt.key ?? opt.label ?? JSON.stringify(opt),
        );
    }
    return String(opt);
}

/** Try to parse a value that may be JSON; otherwise return original. */
function tryParseMaybeJson(v) {
    if (v === null || v === undefined) return v;
    if (typeof v !== 'string') return v;
    try {
        return JSON.parse(v);
    } catch (e) {
        return v;
    }
}

/**
 * Normalize any stored answer/correct value to an array of "normalized" strings.
 * Accepts:
 *  - scalar primitive (number/string) -> ["..."]
 *  - JSON string (e.g. "[1,2]") -> array
 *  - array -> array
 *  - object -> single string of its value
 */
function normalizeStoredToArray(val) {
    if (val === null || val === undefined) return [];
    const parsed = tryParseMaybeJson(val);
    if (Array.isArray(parsed)) return parsed.map((v) => String(v));
    if (typeof parsed === 'object') {
        // object (not array) -> try to extract value then stringify
        const v =
            parsed.value ?? parsed.id ?? parsed.key ?? parsed.label ?? parsed;
        return [String(v)];
    }
    return [String(parsed)];
}

/**
 * If the stored value is a numeric index (string or number) and question.options exists,
 * map index -> optionValue. Handles both 1-based and 0-based indexes.
 *
 * Example:
 *  - options: ["Berlin","Paris","Madrid"]
 *  - stored: "2"  => treat as index 2 => check both options[2] (0-based -> "Madrid")
 *                 but also try 1-based: options[1] -> "Paris"
 * We prefer the option that equals optValue when comparing; otherwise we fallback to raw string compare.
 */
function mapIndexToOptionValueIfPossible(stored, questionOptions) {
    if (stored === null || stored === undefined) return null;
    // try parse numeric
    const num = Number(stored);
    if (
        !Number.isNaN(num) &&
        Array.isArray(questionOptions) &&
        questionOptions.length > 0
    ) {
        // try 0-based
        const zero = questionOptions[num];
        if (zero !== undefined) return String(getOptionValue(zero));
        // try 1-based
        const one = questionOptions[num - 1];
        if (one !== undefined) return String(getOptionValue(one));
    }
    return null;
}

/* ---------- Selection / correctness checks (robust) ---------- */

/**
 * Return true if the option 'opt' is among the student's selected option(s).
 * ans: the student answer object (has selected_option; may include question.options)
 */
function isSelected(opt, ans) {
    const optValue = getOptionValue(opt);

    // 1) Normalize stored selected_option to strings
    const stored = tryParseMaybeJson(ans.selected_option);
    // If stored is numeric or string numeric and question options exist, map to option value
    if (
        (typeof stored === 'number' ||
            (typeof stored === 'string' && stored.trim() !== '')) &&
        Array.isArray(ans.question?.options)
    ) {
        const mapped = mapIndexToOptionValueIfPossible(
            stored,
            ans.question.options,
        );
        if (mapped !== null) return String(mapped) === String(optValue);
    }

    // If stored is array, map each element: try index->optionValue mapping, else use string of value
    const arr = Array.isArray(stored) ? stored : normalizeStoredToArray(stored);
    for (const item of arr) {
        // try map index to option string using question.options if available
        if (Array.isArray(ans.question?.options)) {
            const mapped = mapIndexToOptionValueIfPossible(
                item,
                ans.question.options,
            );
            if (mapped !== null && String(mapped) === String(optValue))
                return true;
        }
        if (String(item) === String(optValue)) return true;
        // also allow matching against option.value if opt is object - handled by getOptionValue
    }
    return false;
}

/**
 * Return true if the option 'opt' is one of the correct answer(s).
 * ans.question?.correct_answer or ans.correct_answer may be used.
 */
function isCorrectOption(opt, ans) {
    const optValue = getOptionValue(opt);

    const corrRaw =
        ans.question && ans.question.correct_answer !== undefined
            ? ans.question.correct_answer
            : ans.correct_answer;
    const corrParsed = tryParseMaybeJson(corrRaw);

    // If numeric index and options exist -> map index to option
    if (
        (typeof corrParsed === 'number' ||
            (typeof corrParsed === 'string' && corrParsed.trim() !== '')) &&
        Array.isArray(ans.question?.options)
    ) {
        const mapped = mapIndexToOptionValueIfPossible(
            corrParsed,
            ans.question.options,
        );
        if (mapped !== null) return String(mapped) === String(optValue);
    }

    const arr = Array.isArray(corrParsed)
        ? corrParsed
        : normalizeStoredToArray(corrParsed);
    for (const item of arr) {
        // try map index to option if possible
        if (Array.isArray(ans.question?.options)) {
            const mapped = mapIndexToOptionValueIfPossible(
                item,
                ans.question.options,
            );
            if (mapped !== null && String(mapped) === String(optValue))
                return true;
        }
        if (String(item) === String(optValue)) return true;
    }
    return false;
}

/* ---------- Option rendering & classes (keeps your UI intent) ---------- */
function optionClass(opt, ans) {
    const base = 'w-full';
    const correct = isCorrectOption(opt, ans);
    const selected = isSelected(opt, ans);

    if (correct && selected) return base + ' border-green-500 bg-green-300';
    if (correct) return base + ' border-green-200 bg-green-100';
    if (selected) return base + ' border-red-200 bg-red-100';
    return base;
}

function renderOption(opt) {
    if (opt && typeof opt === 'object') {
        // prefer label (safe for HTML), fallback to value
        return opt.label ?? opt.value ?? JSON.stringify(opt);
    }
    return String(opt ?? '');
}

/* LOG helpers */
function logLabel(evt) {
    if (!evt) return 'Event';
    const map = {
        submit: 'Submitted',
        autosave: 'Autosave',
        manual_mark_update: 'Manual mark updated',
        manual_mark_create: 'Manual mark created',
        start: 'Started',
    };
    return map[evt] ?? evt.charAt(0).toUpperCase() + evt.slice(1);
}
function logTagClass(evt) {
    const base = 'px-2 py-1 rounded text-xs font-medium';
    // if (evt === 'submit' || evt === 'regraded')
    //     return base + ' bg-blue-50 text-blue-700';
    if (evt === 'manual_mark_update' || evt === 'manual_mark_create')
        return base + ' bg-indigo-50 text-indigo-700';
    if (evt === 'autosave') return base + ' bg-gray-50 text-gray-700';
    return base + ' bg-gray-50 text-gray-700';
}
function prettyMeta(m) {
    try {
        if (!m) return '';
        if (typeof m === 'object') return JSON.stringify(m, null, 2);
        // maybe it's a JSON string
        const parsed = JSON.parse(m);
        return JSON.stringify(parsed, null, 2);
    } catch (_e) {
        return String(m);
    }
}
function relativeTime(iso) {
    if (!iso) return '';
    const d = new Date(iso);
    const diff = Date.now() - d.getTime();
    const s = Math.floor(diff / 1000);
    if (s < 60) return `${s}s ago`;
    const m = Math.floor(s / 60);
    if (m < 60) return `${m}m ago`;
    const h = Math.floor(m / 60);
    if (h < 24) return `${h}h ago`;
    const days = Math.floor(h / 24);
    return `${days}d ago`;
}
function shortUA(ua) {
    if (!ua) return '';
    // naive: show only first 60 chars
    return ua.length > 60 ? ua.slice(0, 60) + '…' : ua;
}
</script>

<style scoped>
/* small color aliases used above (bg-green-25 doesn't exist by default; emulate very light) */
.bg-green-25 {
    background-color: rgba(16, 185, 129, 0.06);
} /* subtle green tint */
</style>
