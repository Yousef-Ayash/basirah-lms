<script setup>
import StudentLayout from '@/Pages/Student/Layout.vue';
import Card from '@/components/LayoutStructure/Card.vue';
import SectionHeader from '@/components/LayoutStructure/SectionHeader.vue';

import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

defineOptions({ layout: StudentLayout });

const props = defineProps({
    attempt: Object,
    exam: Object,
    questions: Array,
    answers: Object,
});

// The controller flashes these flags to the session on submit
const canReview = computed(() => usePage().props.flash.review ?? props.exam.review_allowed);
const showAnswers = computed(() => usePage().props.flash.show_answers ?? false);

// Helper to quickly look up a student's answer for a given question
const getStudentAnswer = (questionId) => {
    return props.answers[questionId] ?? null;
};
</script>

<template>
    <div class="mx-auto max-w-3xl">
        <Head :title="`نتيجة اختبار: ${exam.title}`" />
        <SectionHeader title="نتيجة الاختبار" />

        <Card class="mb-6 text-center">
            <h2 class="text-xl font-bold">{{ exam.title }}</h2>
            <p class="text-gray-500">{{ `تم الإرسال في ${new Date(attempt.submitted_at).toLocaleString()}` }}</p>
            <div class="my-4">
                <p class="text-lg">درجتك:</p>
                <p v-if="attempt.score !== null" class="text-5xl font-bold text-green-500">{{ attempt.score }}%</p>
                <p v-else class="text-2xl font-bold text-gray-500">لم يتم التصحيح بعد</p>
            </div>
        </Card>

        <div v-if="canReview">
            <h3 class="mb-2 text-lg font-semibold">مراجعة الإجابات</h3>
            <div class="space-y-4">
                <Card v-for="question in questions" :key="question.id">
                    <p class="mb-2 border-b pb-2 font-semibold dark:border-gray-700">{{ question.question_text }}</p>
                    <ul class="space-y-1 text-sm">
                        <li
                            v-for="(option, index) in question.options"
                            :key="index"
                            :class="{
                                'font-bold text-green-500': showAnswers && index + 1 === question.correct_answer,
                                'text-red-500':
                                    getStudentAnswer(question.id) === index + 1 && (!showAnswers || index + 1 !== question.correct_answer),
                            }"
                        >
                            {{ option }}
                            <span v-if="getStudentAnswer(question.id) === index + 1"> &larr; إجابتك</span>
                            <span v-if="showAnswers && index + 1 === question.correct_answer"> ✓ (الإجابة الصحيحة)</span>
                        </li>
                    </ul>
                </Card>
            </div>
        </div>
        <div v-else>
            <Card class="p-6 text-center">
                <p>مراجعة الإجابات غير مفعلة لهذا الاختبار.</p>
            </Card>
        </div>
    </div>
</template>
