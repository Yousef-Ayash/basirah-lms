<script setup>
import StudentLayout from '@/Pages/Student/Layout.vue';
import BaseButton from '@/components/FormElements/BaseButton.vue';
import Card from '@/components/LayoutStructure/Card.vue';
import ConfirmDialog from '@/components/Misc/ConfirmDialog.vue';

import { Head, useForm } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, reactive, ref, watch } from 'vue';

defineOptions({ layout: StudentLayout });

const props = defineProps({
    exam: Object,
    attempt: Object,
    questions: Array,
});

// ------------------ state ------------------
const currentIndex = ref(0);
const answers = reactive({}); // answers map: question_id => selected_option (index or value)
const showConfirm = ref(false);

// replaces showReloadModal: now used as "restore saved answers?" modal
const showRestoreModal = ref(false);

// form for Inertia post
const form = useForm({
    answers: {},
});

// autosave config
const ATTEMPT_KEY = `attempt:${props.attempt.id}:answers`;
const ATTEMPT_SESSION_FLAG = `attempt:${props.attempt.id}:loaded`;
const LAST_SAVED_KEY = `attempt:${props.attempt.id}:last_saved`;
const AUTO_SAVE_INTERVAL_MS = 2500; // autosave interval
let autosaveTimer = null;
let periodicTimer = null;

// helper to serialize answers (strip reactivity)
function snapshotAnswers() {
    // copy only own properties
    const out = {};
    for (const k in answers) {
        if (Object.prototype.hasOwnProperty.call(answers, k))
            out[k] = answers[k];
    }
    return out;
}

// save to localStorage
function saveToStorage() {
    try {
        const data = {
            answers: snapshotAnswers(),
            saved_at: new Date().toISOString(),
        };
        localStorage.setItem(ATTEMPT_KEY, JSON.stringify(data));
        localStorage.setItem(LAST_SAVED_KEY, data.saved_at);
    } catch (e) {
        // ignore quota errors; do not break exam
        // optionally you could show a toast here
        // console.warn('Autosave failed', e);
    }
}

// load from localStorage; returns null if none
function loadFromStorage() {
    try {
        const raw = localStorage.getItem(ATTEMPT_KEY);
        if (!raw) return null;
        const parsed = JSON.parse(raw);
        return parsed;
    } catch (e) {
        return null;
    }
}

// remove saved storage
function clearStorage() {
    try {
        localStorage.removeItem(ATTEMPT_KEY);
        localStorage.removeItem(LAST_SAVED_KEY);
    } catch (e) {}
}

// ------------------ submit ------------------
const submitExam = () => {
    // copy answers into form and submit; ensure cleanup after success
    form.answers = { ...snapshotAnswers() };

    // attach onSuccess hook to clear storage
    form.post(route('attempts.submit', { attempt: props.attempt.id }), {
        onSuccess: () => {
            // Clear autosave data on successful submit
            try {
                clearStorage();
                sessionStorage.removeItem(ATTEMPT_SESSION_FLAG);
            } catch (e) {}
        },
    });
};

// ------------------ autosave (debounced) ------------------

// schedule a debounced save
function scheduleSaveDebounced() {
    if (autosaveTimer) clearTimeout(autosaveTimer);
    autosaveTimer = setTimeout(() => {
        saveToStorage();
        autosaveTimer = null;
    }, 400); // small debounce for frequent interactions
}

// periodic save to ensure data is persisted even if no interaction
function startPeriodicSave() {
    if (periodicTimer) clearInterval(periodicTimer);
    periodicTimer = setInterval(() => {
        saveToStorage();
    }, AUTO_SAVE_INTERVAL_MS);
}
function stopPeriodicSave() {
    if (periodicTimer) {
        clearInterval(periodicTimer);
        periodicTimer = null;
    }
}

// watch answers for changes and schedule save
watch(
    () => snapshotAnswers(),
    () => {
        scheduleSaveDebounced();
    },
    { deep: true },
);

// ------------------ timer logic (unchanged) ------------------
const timeLimitInSeconds = props.exam.time_limit_minutes * 60;
const startedAt = new Date(props.attempt.started_at);
const now = ref(new Date());
let timerInterval = null;

const elapsedTime = computed(() => Math.floor((now.value - startedAt) / 1000));
const timeLeft = computed(() =>
    Math.max(0, timeLimitInSeconds - elapsedTime.value),
);
const minutesLeft = computed(() =>
    String(Math.floor(timeLeft.value / 60)).padStart(2, '0'),
);
const secondsLeft = computed(() =>
    String(timeLeft.value % 60).padStart(2, '0'),
);

// ------------------ lifecycle ------------------
onMounted(() => {
    // start timers
    timerInterval = setInterval(() => {
        now.value = new Date();
        if (timeLeft.value <= 0) {
            // final save just in case
            saveToStorage();
            submitExam();
        }
    }, 1000);
    startPeriodicSave();

    // detect reload / navigation in same tab session
    try {
        const prevFlag = sessionStorage.getItem(ATTEMPT_SESSION_FLAG);
        // set the flag now (first-time or reload)
        sessionStorage.setItem(ATTEMPT_SESSION_FLAG, String(Date.now()));

        // If flag existed already, this is a reload or navigation-back inside same tab session
        if (prevFlag) {
            // If we have saved answers, offer to restore
            const saved = loadFromStorage();
            if (
                saved &&
                saved.answers &&
                Object.keys(saved.answers).length > 0
            ) {
                showRestoreModal.value = true;
            } else {
                // no saved answers; you may optionally notify user
            }
        }
    } catch (e) {
        // silent
    }

    // `beforeunload` warning to prevent accidental leave (close tab/navigation)
    window.addEventListener('beforeunload', onBeforeUnload);

    // Also listen to visibility change to optionally force save
    document.addEventListener('visibilitychange', onVisibilityChange);
});

onUnmounted(() => {
    // cleanup timers and listeners
    if (timerInterval) clearInterval(timerInterval);
    if (autosaveTimer) clearTimeout(autosaveTimer);
    stopPeriodicSave();
    window.removeEventListener('beforeunload', onBeforeUnload);
    document.removeEventListener('visibilitychange', onVisibilityChange);
});

// beforeunload handler: warn user and do a final save
function onBeforeUnload(e) {
    try {
        saveToStorage(); // final save
    } catch (er) {}
    // Standard message is ignored by modern browsers, but returning value triggers confirmation
    e.preventDefault();
    e.returnValue = '';
    return '';
}

// visibilitychange: when tab hidden we save
function onVisibilityChange() {
    if (document.visibilityState === 'hidden') {
        saveToStorage();
    }
}

// ------------------ Restore / Discard actions ------------------
function restoreSavedAnswers() {
    const saved = loadFromStorage();
    if (!saved) {
        showRestoreModal.value = false;
        return;
    }
    const savedAnswers = saved.answers || {};
    // copy saved answers into reactive `answers`
    for (const k in savedAnswers) {
        answers[k] = savedAnswers[k];
    }
    // ensure reactive form copy too
    form.answers = { ...savedAnswers };
    showRestoreModal.value = false;
}

function discardSavedAnswers() {
    clearStorage();
    // reset answers object
    for (const k in answers) delete answers[k];
    form.answers = {};
    showRestoreModal.value = false;
}

// ------------------ small helper to load initial attempt answers if server provided them ------------------
// If your server may provide existing answers (e.g., resumed attempt), initialize answers with attempt.answers
if (
    props.attempt &&
    Array.isArray(props.attempt.answers) &&
    props.attempt.answers.length > 0
) {
    // answers might be array of objects with question_id & selected_option
    try {
        props.attempt.answers.forEach((a) => {
            if (a.question_id !== undefined)
                answers[a.question_id] =
                    a.selected_option ?? a.answer_text ?? null;
        });
    } catch (e) {}
}
</script>

<template>
    <div v-if="questions.length > 0">
        <Head :title="`جاري الاختبار: ${exam.title}`" />
        <div class="mb-4 flex items-center justify-between">
            <h1 class="text-2xl font-bold">{{ exam.title }}</h1>
            <div
                v-if="exam.time_limit_minutes"
                class="rounded-md bg-red-500 px-3 py-1 font-mono text-xl text-white"
            >
                {{ minutesLeft }}:{{ secondsLeft }}
            </div>
        </div>

        <div
            v-if="attempt.paused_at"
            class="mb-4 rounded bg-yellow-50 p-3 text-yellow-800"
        >
            هذه المحاولة في وضع الإيقاف مؤقتًا منذ
            {{ new Date(attempt.paused_at).toLocaleString() }} — اضغط استئناف
            للمتابعة.
        </div>

        <Card>
            <div class="p-4">
                <p class="mb-4 font-semibold">
                    {{ currentIndex + 1 }}.
                    {{ questions[currentIndex].question_text }}
                </p>
                <div class="space-y-3">
                    <label
                        v-for="(option, index) in questions[currentIndex]
                            .options"
                        :key="index"
                        class="flex cursor-pointer items-center space-x-3 rounded-lg border p-3 has-[:checked]:border-green-500 has-[:checked]:bg-green-50 dark:border-gray-700 dark:has-[:checked]:bg-green-900/50"
                    >
                        <input
                            type="radio"
                            :name="`q_${questions[currentIndex].id}`"
                            :value="index + 1"
                            v-model="answers[questions[currentIndex].id]"
                            class="accent-[#61CE70]"
                        />
                        <span>{{ option }}</span>
                    </label>
                </div>
            </div>
        </Card>

        <div class="mt-6 flex items-center justify-between">
            <BaseButton @click="currentIndex--" :disabled="currentIndex === 0"
                >السابق</BaseButton
            >
            <span>{{ `سؤال ${currentIndex + 1} من ${questions.length}` }}</span>
            <BaseButton
                v-if="currentIndex < questions.length - 1"
                @click="currentIndex++"
                >التالي</BaseButton
            >
            <BaseButton
                v-else
                @click="showConfirm = true"
                class="bg-green-600 hover:bg-green-700"
                >إرسال الاختبار</BaseButton
            >
        </div>

        <!-- submit confirmation -->
        <ConfirmDialog
            :show="showConfirm"
            title="تسليم الاختبار"
            message="هل أنت متأكد من أنك تريد تسليم إجاباتك؟ لا يمكن التراجع عن هذا الإجراء."
            @confirm="submitExam"
            @cancel="showConfirm = false"
        />

        <!-- reload notification (simple modal; uses same ConfirmDialog component) -->
        <!-- <ConfirmDialog
            :show="showReloadModal"
            title="تم تحديث الصفحة"
            message="تم تحديث الصفحة. يرجى إعادة المحاولة."
            @confirm="showReloadModal = false"
            @cancel="showReloadModal = false"
        /> -->
        <!-- restore saved answers modal -->
        <ConfirmDialog
            :show="showRestoreModal"
            title="تم تحديث الصفحة — استعادة الإجابات؟"
            message="لقد تم اكتشاف محاولة سابقة أو إعادة تحميل الصفحة. هل تريد استعادة إجاباتك المحفوظة أم حذفها؟"
            @confirm="restoreSavedAnswers"
            @cancel="discardSavedAnswers"
        />
    </div>
    <div v-else>
        <Card class="p-8 text-center">
            <h1 class="text-xl font-bold text-red-500">خطأ</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                تعذر تحميل هذا الاختبار لأنه لا يحتوي على أسئلة.
            </p>
        </Card>
    </div>
</template>
