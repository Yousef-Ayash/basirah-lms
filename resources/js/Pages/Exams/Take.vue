<script setup>
import StudentLayout from '@/Pages/Student/Layout.vue';
import BaseButton from '@/components/FormElements/BaseButton.vue';
import Card from '@/components/LayoutStructure/Card.vue';
import ConfirmDialog from '@/components/Misc/ConfirmDialog.vue';
import { useTranslations } from '@/composables/useTranslations';
import { Head, useForm } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, reactive, ref } from 'vue';

const { __ } = useTranslations();

defineOptions({ layout: StudentLayout });

const props = defineProps({
    exam: Object,
    attempt: Object,
    questions: Array,
});

const currentIndex = ref(0);
const answers = reactive({});
const showConfirm = ref(false);

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
const timeLeft = computed(() => Math.max(0, timeLimitInSeconds - elapsedTime.value));
const minutesLeft = computed(() => String(Math.floor(timeLeft.value / 60)).padStart(2, '0'));
const secondsLeft = computed(() => String(timeLeft.value % 60).padStart(2, '0'));

onMounted(() => {
    timerInterval = setInterval(() => {
        now.value = new Date();
        if (timeLeft.value <= 0) {
            submitExam();
        }
    }, 1000);
});
onUnmounted(() => clearInterval(timerInterval));
</script>

<template>
    <div v-if="questions.length > 0">
        <Head :title="__('student.taking_exam', { title: exam.title })" />
        <div class="mb-4 flex items-center justify-between">
            <h1 class="text-2xl font-bold">{{ exam.title }}</h1>
            <div v-if="exam.time_limit_minutes" class="rounded-md bg-red-500 px-3 py-1 font-mono text-xl text-white">
                {{ minutesLeft }}:{{ secondsLeft }}
            </div>
        </div>

        <Card>
            <div class="p-4">
                <p class="mb-4 font-semibold">{{ currentIndex + 1 }}. {{ questions[currentIndex].question_text }}</p>
                <div class="space-y-3">
                    <label
                        v-for="(option, index) in questions[currentIndex].options"
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
            <BaseButton @click="currentIndex--" :disabled="currentIndex === 0">{{ __('student.previous_question') }}</BaseButton>
            <span>{{ __('student.question_of_total', { current: currentIndex + 1, total: questions.length }) }}</span>
            <BaseButton v-if="currentIndex < questions.length - 1" @click="currentIndex++">{{ __('student.next_question') }}</BaseButton>
            <BaseButton v-else @click="showConfirm = true" class="bg-green-600 hover:bg-green-700">{{ __('buttons.submit_exam') }}</BaseButton>
        </div>

        <ConfirmDialog
            :show="showConfirm"
            :title="__('student.submit_exam_title')"
            :message="__('student.submit_exam_confirm')"
            @confirm="submitExam"
            @cancel="showConfirm = false"
        />
    </div>
    <div v-else>
        <Card class="p-8 text-center">
            <h1 class="text-xl font-bold text-red-500">{{ __('common.error') }}</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">{{ __('student.exam_load_error') }}</p>
        </Card>
    </div>
</template>
