<script>
import AdminLayout from '@/Pages/Admin/Layout.vue';
import StudentLayout from '@/Pages/Student/Layout.vue';
import { usePage, Head } from '@inertiajs/vue3';

export default {
    layout: (h, page) => {
        const user = usePage().props.auth.user;
        const isAdmin = user.roles?.some((role) => role.name === 'admin');
        const LayoutComponent = isAdmin ? AdminLayout : StudentLayout;
        return h(LayoutComponent, [page]);
    },
};
</script>

<script setup>
import BaseButton from '@/components/FormElements/BaseButton.vue';
import Card from '@/components/LayoutStructure/Card.vue';
import SectionHeader from '@/components/LayoutStructure/SectionHeader.vue';

const props = defineProps({
    attempt: Object,
    exam: Object,
    questions: Array,
    answers: Object, // Key-value pair of { question_id: selected_option }
});

// Create a map for quick answer lookup
const answerMap = new Map(Object.entries(props.answers));
</script>

<template>
    <div class="mx-auto max-w-3xl">
        <Head :title="`نتيجة اختبار: ${exam.title}`" />
        <SectionHeader title="نتيجة الاختبار">
            <template #action>
                <BaseButton as="a" :href="route('attempts.download', attempt.id)"> تحميل كـ PDF </BaseButton>
            </template>
        </SectionHeader>

        <Card class="mb-6 text-center">
            <h2 class="text-xl font-bold">{{ exam.title }}</h2>
            <p class="text-gray-500">{{ `تم الإرسال في ${new Date(attempt.submitted_at).toLocaleString()}` }}</p>
            <div class="my-4">
                <p class="text-lg">درجتك:</p>
                <p class="text-5xl font-bold text-green-500">{{ attempt.score }}%</p>
            </div>
        </Card>

        <div v-if="exam.review_allowed">
            <h3 class="mb-2 text-lg font-semibold">مراجعة الإجابات</h3>
            <div class="space-y-4">
                <Card v-for="question in questions" :key="question.id">
                    <p class="mb-2 border-b pb-2 font-semibold dark:border-gray-700">{{ question.question_text }}</p>
                    <ul class="space-y-1 text-sm">
                        <li
                            v-for="(option, index) in question.options"
                            :key="index"
                            :class="{
                                'font-bold text-green-500': index + 1 === question.correct_answer, // Correct answer
                                'text-red-500': index + 1 === answerMap.get(String(question.id)) && index + 1 !== question.correct_answer, // Incorrect student answer
                            }"
                        >
                            {{ option }}
                            <span v-if="index + 1 === question.correct_answer"> ✓ (الإجابة الصحيحة)</span>
                            <span v-if="index + 1 === answerMap.get(String(question.id)) && index + 1 !== question.correct_answer">
                                ✗ (إجابتك)</span
                            >
                        </li>
                    </ul>
                </Card>
            </div>
        </div>
    </div>
</template>
