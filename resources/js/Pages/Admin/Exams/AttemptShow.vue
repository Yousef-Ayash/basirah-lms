<script setup>
import AdminLayout from '@/Pages/Admin/Layout.vue';
import Card from '@/components/LayoutStructure/Card.vue';
import { useTranslations } from '@/composables/useTranslations';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

const { __ } = useTranslations();

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
        <Head :title="__('admin.attempt_details')" />
        <h1 class="text-2xl font-bold">{{ __('admin.attempt_details') }}</h1>
        <p class="mb-4 text-sm text-gray-500">
            {{ __('admin.attempt_details_subtitle', { exam: attempt.exam.title, student: attempt.user.name }) }}
        </p>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="space-y-4 lg:col-span-2">
                <h2 class="text-lg font-semibold">{{ __('common.answers') }}</h2>
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
                                <span v-if="getStudentAnswer(question.id) === index + 1"> &larr; {{ __('admin.student_answer') }}</span>
                                <!-- Indicator for the correct answer -->
                                <span v-if="index + 1 === question.correct_answer"> ✓ ({{ __('student.correct_answer') }})</span>
                            </li>
                        </ul>
                    </Card>
                </div>
            </div>
            <div class="space-y-4">
                <h2 class="text-lg font-semibold">{{ __('admin.audit_logs') }}</h2>
                <Card>
                    <ul v-if="logs.length" class="divide-y dark:divide-gray-700">
                        <li v-for="log in logs" :key="log.id" class="p-2 text-xs">
                            <div class="font-semibold">
                                {{
                                    __('admin.log_action_at', {
                                        action: log.action.toUpperCase(),
                                        time: new Date(log.created_at).toLocaleTimeString(),
                                    })
                                }}
                            </div>
                            <div class="text-gray-500">{{ __('labels.ip_address') }}: {{ log.ip }}</div>
                        </li>
                    </ul>
                    <p v-else class="p-4 text-sm text-gray-500">{{ __('admin.no_logs_found') }}</p>
                </Card>
            </div>
        </div>
    </div>
</template>
