<script setup>
import AdminLayout from '@/Pages/Admin/Layout.vue';
import Card from '@/components/LayoutStructure/Card.vue';

import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    attempt: Object,
    logs: Array,
    questions: Array,
});

// Create a map for quick answer lookup from the 'answers' array on the attempt object.
const answerMap = computed(() => {
    const map = new Map();
    if (props.attempt && props.attempt.answers) {
        props.attempt.answers.forEach((answer) => {
            map.set(answer.question_id, answer.selected_option);
        });
    }
    return map;
});

// Helper function to get the student's selected option for a specific question.
const getStudentAnswer = (questionId) => {
    return answerMap.value.get(questionId) ?? null;
};
</script>

<template>
    <div>
        <Head title="تفاصيل المحاولة" />
        <h1 class="text-2xl font-bold">تفاصيل المحاولة</h1>
        <p class="mb-4 text-sm text-gray-500">
            {{ `الاختبار: ${attempt.exam.title} | الطالب: ${attempt.user.name}` }}
        </p>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="space-y-4 lg:col-span-2">
                <h2 class="text-lg font-semibold">الإجابات</h2>
                <div class="space-y-4">
                    <!-- Loop through each question passed from the controller -->
                    <Card v-for="question in questions" :key="question.id">
                        <p class="mb-2 border-b pb-2 font-semibold dark:border-gray-700">{{ question.question_text }}</p>
                        <ul class="space-y-1 text-sm">
                            <!-- Loop through the options for the current question -->
                            <li
                                v-for="(option, index) in question.options"
                                :key="index"
                                :class="{
                                    // Style for the correct answer
                                    'font-bold text-green-500': index + 1 === question.correct_answer,
                                    // Style for the student's incorrect answer
                                    'text-red-500': getStudentAnswer(question.id) === index + 1 && index + 1 !== question.correct_answer,
                                }"
                            >
                                {{ option }}
                                <!-- Indicator for the student's selected answer -->
                                <span v-if="getStudentAnswer(question.id) === index + 1"> &larr; إجابة الطالب</span>
                                <!-- Indicator for the correct answer -->
                                <span v-if="index + 1 === question.correct_answer"> ✓ (الإجابة الصحيحة)</span>
                            </li>
                        </ul>
                    </Card>
                </div>
            </div>
            <div class="space-y-4">
                <h2 class="text-lg font-semibold">سجلات التدقيق</h2>
                <Card>
                    <ul v-if="logs.length" class="divide-y dark:divide-gray-700">
                        <li v-for="log in logs" :key="log.id" class="p-2 text-xs">
                            <div class="font-semibold">
                                {{ `${log.action.toUpperCase()} في ${new Date(log.created_at).toLocaleTimeString()}` }}
                            </div>
                            <div class="text-gray-500">عنوان IP: {{ log.ip }}</div>
                        </li>
                    </ul>
                    <p v-else class="p-4 text-sm text-gray-500">لم يتم العثور على سجلات لهذه المحاولة.</p>
                </Card>
            </div>
        </div>
    </div>
</template>
