<template>
    <div>
        <Head :title="`الاختبار: ${exam.title}`" />
        <div
            class="mb-4 flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center"
        >
            <div>
                <h1 class="text-2xl font-bold">
                    {{ `الاختبار: ${exam.title}` }}
                </h1>
                <p class="text-sm text-gray-500">
                    المادة: {{ exam.subject.title }}
                </p>
            </div>
            <div class="flex flex-shrink-0 gap-2">
                <BaseButton
                    as="a"
                    :href="route('admin.exams.attempts.index', exam.id)"
                    >عرض المحاولات</BaseButton
                >
                <BaseButton
                    as="a"
                    :href="route('admin.exams.questions.export', exam.id)"
                    class="bg-gray-700 hover:bg-gray-800"
                >
                    تصدير الأسئلة
                </BaseButton>
                <BaseButton @click="showQuestionManager = true"
                    >إضافة أسئلة</BaseButton
                >
            </div>
        </div>

        <div class="mt-6 mb-2 flex items-center justify-between">
            <h2 class="text-lg font-semibold">
                {{ `الأسئلة الحالية (${exam.questions.length})` }}
            </h2>
            <BaseButton
                v-if="exam.questions.length > 0"
                @click="showConfirmDeleteAll = true"
                class="bg-red-700 text-sm hover:bg-red-800"
            >
                حذف الكل
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
                            تعديل
                        </BaseButton>
                        <BaseButton
                            @click="deleteQuestion(question.id)"
                            class="bg-red-500 text-sm hover:bg-red-600"
                        >
                            حذف
                        </BaseButton>
                    </div>
                </li>
            </ul>
        </Card>
        <EmptyState v-else message="لم تتم إضافة أي أسئلة لهذا الاختبار بعد." />

        <QuestionManager
            :show="showQuestionManager"
            :exam="exam"
            @close="showQuestionManager = false"
        />

        <ConfirmDialog
            :show="showConfirmDeleteAll"
            title="حذف جميع الأسئلة"
            @confirm="deleteAllQuestions"
            @cancel="showConfirmDeleteAll = false"
        >
            هل أنت متأكد من أنك تريد
            <strong class="text-red-600">حذف جميع</strong>
            الأسئلة لهذا الاختبار؟ لا يمكن التراجع عن هذا الإجراء.
        </ConfirmDialog>
    </div>
</template>

<script setup>
import AdminLayout from '@/Pages/Admin/Layout.vue';
import QuestionManager from '@/components/Exams/QuestionManager.vue';
import BaseButton from '@/components/FormElements/BaseButton.vue';
import Card from '@/components/LayoutStructure/Card.vue';
import ConfirmDialog from '@/components/Misc/ConfirmDialog.vue';
import EmptyState from '@/components/Misc/EmptyState.vue';

import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    exam: Object,
});

const showQuestionManager = ref(false);
const showConfirmDeleteAll = ref(false);

const deleteQuestion = (questionId) => {
    if (confirm('هل أنت متأكد أنك تريد حذف هذا السؤال؟')) {
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
