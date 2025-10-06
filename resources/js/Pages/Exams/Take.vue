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

const currentIndex = ref(0);
const answers = reactive({});
const showConfirm = ref(false);

// NEW: show modal if page was reloaded
const showReloadModal = ref(false);

const form = useForm({
    answers: {},
});

const submitExam = () => {
    form.answers = { ...answers };
    form.post(route('attempts.submit', { attempt: props.attempt.id }));
};

// --- Timer Logic ---
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

onMounted(() => {
    timerInterval = setInterval(() => {
        now.value = new Date();
        if (timeLeft.value <= 0) {
            submitExam();
        }
    }, 1000);

    // --- Detect page reload and show notification ---
    try {
        let navType = null;
        // Prefer Navigation Timing Level 2
        const entries =
            performance.getEntriesByType &&
            performance.getEntriesByType('navigation');
        if (entries && entries[0] && entries[0].type) {
            navType = entries[0].type; // 'navigate' | 'reload' | 'back_forward' | 'prerender'
        } else if (
            performance.navigation &&
            typeof performance.navigation.type !== 'undefined'
        ) {
            // Fallback for older browsers: 1 === TYPE_RELOAD
            navType = performance.navigation.type === 1 ? 'reload' : 'navigate';
        }

        const isReload = navType === 'reload';
        if (isReload) {
            // show modal to inform the user
            showReloadModal.value = true;
        }
    } catch (e) {
        // silently ignore detection errors
        // (detection is best-effort; don't break the exam)
    }
});

onUnmounted(() => clearInterval(timerInterval));
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
            <BaseButton
                @click="currentIndex--"
                :disabled="currentIndex === 0"
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
        <ConfirmDialog
            :show="showReloadModal"
            title="تم تحديث الصفحة"
            message="تم تحديث الصفحة. يرجى إعادة المحاولة."
            @confirm="showReloadModal = false"
            @cancel="showReloadModal = false"
        />
    </div>
    <div v-else>
        <Card class="p-8 text-center">
            <h1 class="text-xl font-bold text-red-500">
                خطأ
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                تعذر تحميل هذا الاختبار لأنه لا يحتوي على أسئلة.
            </p>
        </Card>
    </div>
</template>
