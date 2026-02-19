<script setup>
import StudentLayout from '@/Pages/Student/Layout.vue';
import BaseButton from '@/components/FormElements/BaseButton.vue';
import Card from '@/components/LayoutStructure/Card.vue';
import ConfirmDialog from '@/components/Misc/ConfirmDialog.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, reactive, ref } from 'vue';

defineOptions({ layout: StudentLayout });

const props = defineProps({
    exam: Object,
    attempt: Object,
    questions: Array,
});

// ------------------ state ------------------
const currentIndex = ref(0);
const answers = reactive({});
const showConfirm = ref(false);
const showAbortConfirm = ref(false);

const form = useForm({
    answers: {},
});

// ------------------ timer logic ------------------
// Initialize timeLeft directly from the server provided seconds.
// If null (unlimited time), we set a high number or handle null explicitly.
const timeLeft = ref(props.attempt.remaining_seconds ?? null);

let timerInterval = null;

const minutesLeft = computed(() => {
    if (timeLeft.value === null) return '--';
    return String(Math.floor(timeLeft.value / 60)).padStart(2, '0');
});

const secondsLeft = computed(() => {
    if (timeLeft.value === null) return '--';
    return String(timeLeft.value % 60).padStart(2, '0');
});

// ---------- submit / abort helpers ----------
const csrfToken =
    document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute('content') || '';

const submitExam = () => {
    removeUnloadListeners();
    form.answers = { ...answers };
    form.post(route('attempts.submit', { attempt: props.attempt.id }));
};

const localAbort = async () => {
    removeUnloadListeners();
    await fetch(route('attempts.abort', { attempt: props.attempt.id }), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken, // Ensure token is sent in header too
        },
        body: JSON.stringify({ _token: csrfToken }),
    });
    window.location.href = route('attempts.index');
};

// ---------- unload handlers (No changes here) ----------
function handleBeforeUnload(event) {
    event.preventDefault();
    event.returnValue = '';
    return '';
}

function handlePageHide(event) {
    try {
        const url = route('attempts.abort', { attempt: props.attempt.id });
        // const token = getCsrfToken();

        // try fetch with keepalive (allows including CSRF token and JSON)
        // Note: keepalive requests are best-effort and may be size-limited.
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-XSRF-TOKEN': token,
            },
            // Laravel expects _token in body or X-XSRF-TOKEN header.
            // Sending both is safest.
            body: JSON.stringify({ _token: csrfToken }),
            keepalive: true,
        }).catch(() => {
            // ignore failures here; best-effort. Optionally fallback to sendBeacon (without CSRF).
            try {
                if (navigator.sendBeacon) {
                    const blob = new Blob(
                        [JSON.stringify({ _token: csrfToken })],
                        { type: 'application/json' },
                    );
                    navigator.sendBeacon(url, blob);
                }
            } catch (e) {
                // swallow
            }
        });
    } catch (e) {
        // swallow errors — this is best-effort
    }
}

function addUnloadListeners() {
    window.addEventListener('beforeunload', handleBeforeUnload);
    window.addEventListener('pagehide', handlePageHide);
}

function removeUnloadListeners() {
    window.removeEventListener('beforeunload', handleBeforeUnload);
    window.removeEventListener('pagehide', handlePageHide);
}

// ---------- lifecycle ----------
onMounted(() => {
    // Only start timer if there is a time limit
    if (timeLeft.value !== null) {
        timerInterval = setInterval(() => {
            if (timeLeft.value > 0) {
                timeLeft.value--;
            } else {
                clearInterval(timerInterval);
                submitExam();
            }
        }, 1000);
    }

    addUnloadListeners();
});

onUnmounted(() => {
    if (timerInterval) clearInterval(timerInterval);
    removeUnloadListeners();
});
</script>

<template>
    <div v-if="questions.length > 0">
        <Head :title="`جاري الاختبار: ${exam.title}`" />
        <div class="mb-4 flex items-center justify-between">
            <h1 class="text-2xl font-bold">{{ exam.title }}</h1>

            <div class="flex items-center space-x-4">
                <BaseButton
                    @click="showAbortConfirm = true"
                    class="bg-red-500 text-white hover:bg-red-600"
                >
                    إلغاء الاختبار
                </BaseButton>
                <div
                    v-if="exam.time_limit_minutes"
                    class="rounded-md bg-red-500 px-3 py-1 font-mono text-xl text-white"
                >
                    {{ minutesLeft }}:{{ Math.trunc(secondsLeft) }}
                </div>
            </div>
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

        <ConfirmDialog
            :show="showAbortConfirm"
            title="إلغاء الاختبار"
            confirm-txt="تأكيد"
            @confirm="localAbort"
            @cancel="showAbortConfirm = false"
        >
            هل أنت متأكد من أنك تريد إلغاء الاختبار الآن؟
            <strong class="font-bold text-red-600">
                سيتم اعتبار هذه المحاولة فاشلة اعتمادًا على قواعد الاختبار.
            </strong>
        </ConfirmDialog>
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
