<template>
    <div>
        <Head :title="__('admin.exam_show_title', { title: exam.title })" />
        <div
            class="mb-4 flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center"
        >
            <div>
                <h1 class="text-2xl font-bold">
                    {{ __('admin.exam_header', { title: exam.title }) }}
                </h1>
                <p class="text-sm text-gray-500">
                    {{ __('common.subject') }}: {{ exam.subject.title }}
                </p>
            </div>
            <div class="flex flex-shrink-0 gap-2">
                <BaseButton
                    as="a"
                    :href="route('admin.exams.attempts.index', exam.id)"
                    >{{ __('buttons.view_attempts') }}</BaseButton
                >
                <BaseButton
                    as="a"
                    :href="route('admin.exams.questions.export', exam.id)"
                    class="bg-gray-700 hover:bg-gray-800"
                >
                    {{ __('buttons.export_questions') }}
                </BaseButton>
                <BaseButton @click="showQuestionManager = true">{{
                    __('buttons.add_questions')
                }}</BaseButton>
            </div>
        </div>

        <div class="mt-6 mb-2 flex items-center justify-between">
            <h2 class="text-lg font-semibold">
                {{
                    __('admin.current_questions', {
                        count: exam.questions.length,
                    })
                }}
            </h2>
            <BaseButton
                v-if="exam.questions.length > 0"
                @click="showConfirmDeleteAll = true"
                class="bg-red-700 text-sm hover:bg-red-800"
            >
                {{ __('buttons.delete_all') }}
            </BaseButton>
        </div>

        <Card v-if="exam.questions.length">
            <ul class="divide-y dark:divide-gray-700">
                <li
                    v-for="question in exam.questions"
                    :key="question.id"
                    class="flex items-start justify-between p-3"
                >
                    <div>
                        <p class="font-medium">
                            {{ question.question_text }}
                            <span>( العلامة: {{ question.mark }} )</span>
                        </p>
                        <ul class="mt-1 space-y-1 text-xs text-gray-500">
                            <li
                                v-for="(option, index) in question.options"
                                :key="index"
                                :class="{
                                    'font-bold text-green-500':
                                        index + 1 === question.correct_answer,
                                }"
                            >
                                {{ option }}
                                <span
                                    v-if="index + 1 === question.correct_answer"
                                    >✓</span
                                >
                            </li>
                        </ul>
                    </div>
                    <div class="ms-4 flex flex-shrink-0 gap-2">
                        <BaseButton
                            as="a"
                            :href="
                                route('admin.exams.questions.edit', {
                                    exam: exam.id,
                                    question: question.id,
                                })
                            "
                            class="bg-blue-500 text-sm hover:bg-blue-600"
                        >
                            {{ __('common.edit') }}
                        </BaseButton>
                        <BaseButton
                            @click="deleteQuestion(question.id)"
                            class="bg-red-500 text-sm hover:bg-red-600"
                        >
                            {{ __('common.delete') }}
                        </BaseButton>
                    </div>
                </li>
            </ul>
        </Card>
        <EmptyState v-else :message="__('admin.no_questions_yet')" />

        <QuestionManager
            :show="showQuestionManager"
            :exam="exam"
            @close="showQuestionManager = false"
        />

        <ConfirmDialog
            :show="showConfirmDeleteAll"
            :title="__('admin.delete_all_questions_title')"
            :message="__('messages.delete_all_questions_confirm')"
            @confirm="deleteAllQuestions"
            @cancel="showConfirmDeleteAll = false"
        />
    </div>
</template>

<script setup>
import AdminLayout from '@/Pages/Admin/Layout.vue';
import QuestionManager from '@/components/Exams/QuestionManager.vue';
import BaseButton from '@/components/FormElements/BaseButton.vue';
import Card from '@/components/LayoutStructure/Card.vue';
import ConfirmDialog from '@/components/Misc/ConfirmDialog.vue';
import EmptyState from '@/components/Misc/EmptyState.vue';
import { useTranslations } from '@/composables/useTranslations';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const { __ } = useTranslations();

defineOptions({ layout: AdminLayout });

const props = defineProps({
    exam: Object,
});

const showQuestionManager = ref(false);
const showConfirmDeleteAll = ref(false);

const deleteQuestion = (questionId) => {
    if (confirm(__('messages.delete_question_confirm'))) {
        router.delete(
            route('admin.exams.questions.destroy', {
                exam: props.exam.id,
                question: questionId,
            }),
            {
                preserveScroll: true,
            },
        );
    }
};

const deleteAllQuestions = () => {
    router.delete(
        route('admin.exams.questions.destroy.all', { exam: props.exam.id }),
        {
            onFinish: () => (showConfirmDeleteAll.value = false),
        },
    );
};
</script>
