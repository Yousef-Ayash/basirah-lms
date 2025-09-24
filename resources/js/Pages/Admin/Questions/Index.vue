<template>
    <div>
        <Head :title="__('admin.questions_for', { title: exam.title })" />
        <SectionHeader>
            <template #title>
                <div class="flex items-center space-x-2">
                    <Link :href="route('admin.exams.show', exam.id)" class="text-blue-500 hover:underline">
                        {{ __('common.exam_title', { title: exam.title }) }}
                    </Link>
                    <span class="text-gray-500">/</span>
                    <span>{{ __('common.questions') }}</span>
                </div>
            </template>
            <template #action>
                <BaseButton as="a" :href="route('admin.exams.questions.create', exam.id)"> {{ __('buttons.add_question') }} </BaseButton>
            </template>
        </SectionHeader>

        <Card v-if="questions.data.length">
            <ul class="divide-y dark:divide-gray-700">
                <li v-for="question in questions.data" :key="question.id" class="p-4">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="font-semibold">{{ question.question_text }}</p>
                            <ul class="mt-2 space-y-1 text-sm">
                                <li
                                    v-for="(option, index) in question.options"
                                    :key="index"
                                    :class="{ 'font-bold text-green-500': index + 1 === question.correct_answer }"
                                >
                                    {{ option }} <span v-if="index + 1 === question.correct_answer">✓</span>
                                </li>
                            </ul>
                        </div>
                        <BaseButton @click="deleteQuestion(question.id)" class="bg-red-500 hover:bg-red-600"> {{ __('common.delete') }} </BaseButton>
                    </div>
                </li>
            </ul>
        </Card>
        <EmptyState v-else :message="__('messages.no_questions_yet')" />

        <div class="mt-6">
            <Pagination :links="questions.links" />
        </div>
    </div>
</template>

<script setup>
import AdminLayout from '@/Pages/Admin/Layout.vue';
import BaseButton from '@/components/FormElements/BaseButton.vue';
import Card from '@/components/LayoutStructure/Card.vue';
import Pagination from '@/components/LayoutStructure/Pagination.vue';
import SectionHeader from '@/components/LayoutStructure/SectionHeader.vue';
import EmptyState from '@/components/Misc/EmptyState.vue';
import { useTranslations } from '@/composables/useTranslations';
import { Head, Link, router } from '@inertiajs/vue3';

const { __ } = useTranslations();

defineOptions({ layout: AdminLayout });

const props = defineProps({
    exam: Object,
    questions: Object, // Paginated questions
});

const deleteQuestion = (questionId) => {
    if (confirm('Are you sure you want to delete this question?')) {
        router.delete(route('admin.exams.questions.destroy', { exam: props.exam.id, question: questionId }));
    }
};
</script>
