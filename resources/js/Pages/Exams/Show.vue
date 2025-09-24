<script>
import AdminLayout from '@/Pages/Admin/Layout.vue';
import StudentLayout from '@/Pages/Student/Layout.vue';
import { usePage , Head} from '@inertiajs/vue3';

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
import Card from '@/components/LayoutStructure/Card.vue';
import SectionHeader from '@/components/LayoutStructure/SectionHeader.vue';
import Alert from '@/components/Misc/Alert.vue';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useTranslations } from '@/composables/useTranslations';

const { __ } = useTranslations();

const props = defineProps({
    exam: Object,
    attempts_count: Number,
    can_start: Boolean,
});

// Create computed properties to determine the exam's status
const hasAttemptsLeft = computed(() => props.attempts_count < props.exam.max_attempts);
const isNotYetOpen = computed(() => props.exam.open_at && new Date(props.exam.open_at) > new Date());
const isClosed = computed(() => props.exam.close_at && new Date(props.exam.close_at) < new Date());
const hasNoQuestions = computed(() => props.exam.question_count === 0); // <-- New computed property

// Helper for formatting dates
const formatDate = (dateString) => {
    if (!dateString) return '';
    return new Date(dateString).toLocaleString();
};

const alertInfo = computed(() => {
    if (hasNoQuestions.value) {
        return { type: 'info', message: __('student.exam_not_ready') };
    }
    if (isNotYetOpen.value) {
        return { type: 'info', message: __('student.exam_not_open_yet', { date: formatDate(props.exam.open_at) }) };
    }
    if (isClosed.value) {
        return { type: 'error', message: __('student.exam_closed') };
    }
    if (!hasAttemptsLeft.value) {
        return { type: 'error', message: __('student.no_attempts_left') };
    }
    if (props.can_start) {
        return { type: 'success', message: __('student.exam_ready') };
    }
    return { type: 'error', message: __('student.not_eligible') };
});
</script>

<template>
    <div class="mx-auto max-w-3xl">
        <Head :title="exam.title" />
        <SectionHeader :title="exam.title" />
        <p v-if="exam.description" class="mb-6 text-gray-600 dark:text-gray-400">{{ exam.description }}</p>

        <Card>
            <h3 class="mb-4 text-lg font-bold">{{ __('student.exam_rules_status') }}</h3>
            <ul class="space-y-3 border-t border-b py-4 text-sm dark:border-gray-700">
                <li class="flex justify-between">
                    <span>{{ __('student.time_limit') }}:</span>
                    <span class="font-semibold">{{ exam.time_limit_minutes ? `${exam.time_limit_minutes} ${__('common.minutes')}` : __('student.no_time_limit') }}</span>
                </li>
                <li class="flex justify-between">
                    <span>{{ __('student.max_attempts') }}:</span>
                    <span class="font-semibold">{{ exam.max_attempts }}</span>
                </li>
                <li class="flex justify-between">
                    <span>{{ __('student.your_attempts') }}:</span>
                    <span class="font-semibold">{{ attempts_count }}</span>
                </li>
                <li v-if="exam.open_at" class="flex justify-between">
                    <span>{{ __('student.opens_on') }}:</span>
                    <span class="font-semibold">{{ formatDate(exam.open_at) }}</span>
                </li>
                <li v-if="exam.close_at" class="flex justify-between">
                    <span>{{ __('student.closes_on') }}:</span>
                    <span class="font-semibold">{{ formatDate(exam.close_at) }}</span>
                </li>
            </ul>

            <div class="mt-6 text-center">
                <Alert :type="alertInfo.type" :message="alertInfo.message" />

                <Link
                    :href="route('exams.start', exam.id)"
                    method="post"
                    as="button"
                    :disabled="!can_start"
                    class="mt-4 inline-flex items-center justify-center rounded-lg bg-[#61CE70] px-6 py-3 font-semibold text-white transition hover:bg-[#4CAF60] disabled:cursor-not-allowed disabled:opacity-50"
                >
                    {{ __('buttons.start_exam') }}
                </Link>

                <div v-if="attempts_count > 0" class="mt-4">
                    <Link :href="route('attempts.index')" class="text-sm text-blue-500 hover:underline"> {{ __('student.view_past_attempts') }} </Link>
                </div>
            </div>
        </Card>
    </div>
</template>