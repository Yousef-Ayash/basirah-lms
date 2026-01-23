<script>
import AdminLayout from '@/Pages/Admin/Layout.vue';
import StudentLayout from '@/Pages/Student/Layout.vue';
import { usePage, Head } from '@inertiajs/vue3';
import BaseButton from '@/components/FormElements/BaseButton.vue';

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
import { Link, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import formatMinutes from '@/composables/useFormatMinutes';
import ConfirmDialog from '@/components/Misc/ConfirmDialog.vue';
import BaseButton from '@/components/FormElements/BaseButton.vue';

const props = defineProps({
    exam: Object,
    attempts_count: Number,
    can_start: Boolean,
    // start_token: String,
    next_attempt_date: Date,
    is_passed: Boolean,
});

function getLocalToday() {
    const today = new Date();

    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, '0'); // Months are 0-indexed, so we add 1
    const day = String(today.getDate()).padStart(2, '0');

    const yyyyMMdd = `${year}-${month}-${day}`;

    return yyyyMMdd;
}

// Create computed properties to determine the exam's status
const hasAttemptsLeft = computed(
    () => props.attempts_count < props.exam.max_attempts,
);
const isNotYetOpen = computed(
    () => props.exam.open_at && new Date(props.exam.open_at) > new Date(),
);
const isClosed = computed(
    () => props.exam.close_at && new Date(props.exam.close_at) < new Date(),
);
const hasNoQuestions = computed(() => props.exam.question_count === 0);
const passed = computed(() => props.is_passed);

// Helper for formatting dates
const formatDate = (dateString) => {
    if (!dateString) return '';
    return new Date(dateString).toLocaleString();
};

const alertInfo = computed(() => {
    if (hasNoQuestions.value) {
        return {
            type: 'info',
            message: 'هذا الاختبار قيد الإعداد وغير متاح للبدء بعد.',
        };
    }
    if (isNotYetOpen.value) {
        return {
            type: 'info',
            message: `هذا الاختبار غير متاح بعد. سيفتح في ${formatDate(props.exam.open_at)}.`,
        };
    }
    if (isClosed.value) {
        return {
            type: 'error',
            message: 'تم إغلاق هذا الاختبار ولم يعد متاحًا لمحاولات جديدة.',
        };
    }
    if (!hasAttemptsLeft.value) {
        return {
            type: 'error',
            message: 'لقد استنفدت جميع المحاولات المتاحة لهذا الاختبار.',
        };
    }
    if (passed.value) {
        return {
            type: 'info',
            message: 'لقد اجتزت هذا الاختبار ولا يمكنك تقديمه مجدداً',
        };
    }
    if (props.can_start) {
        return {
            type: 'success',
            message: 'هذا الاختبار مفتوح وأنت جاهز للبدء.',
        };
    }
    return {
        type: 'error',
        message: 'أنت غير مؤهل لبدء هذا الاختبار في الوقت الحالي.',
    };
});

const showConfirm = ref(false);

const startExam = () => {
    // if (!props.start_token) {
    //     alert('لا يمكن بدء الاختبار.');
    //     return;
    // }
    router.post(route('exams.start', props.exam.id));
};
</script>

<template>
    <div class="mx-auto max-w-3xl">
        <Head :title="exam.title" />
        <SectionHeader :title="exam.title" />
        <p
            v-if="exam.description"
            class="mb-6 text-gray-600 dark:text-gray-400"
        >
            {{ exam.description }}
        </p>

        <Card>
            <h3 class="mb-4 text-lg font-bold">قواعد وحالة الاختبار</h3>
            <ul
                class="space-y-3 border-t border-b py-4 text-sm dark:border-gray-700"
            >
                <li class="flex justify-between">
                    <span>الوقت المحدد:</span>
                    <span class="font-semibold">{{
                        formatMinutes(exam.time_limit_minutes).formattedString
                    }}</span>
                </li>
                <li class="flex justify-between">
                    <span>عدد المحاولات المسموح بها:</span>
                    <span class="font-semibold">{{ exam.max_attempts }}</span>
                </li>
                <li class="flex justify-between">
                    <span>محاولاتك:</span>
                    <span class="font-semibold">{{ attempts_count }}</span>
                </li>
                <li class="flex justify-between">
                    <span>العلامة الكاملة:</span>
                    <span class="font-semibold">{{ exam.full_mark }}</span>
                </li>
                <li class="flex justify-between">
                    <span>الحد الأدنى للنجاح:</span>
                    <span class="font-semibold">{{ exam.pass_threshold }}</span>
                </li>
                <li
                    v-if="
                        new Date(getLocalToday()) < new Date(next_attempt_date)
                    "
                    class="flex justify-between"
                >
                    <span>موعد المحاولة التالية:</span>
                    <span class="font-semibold">{{ next_attempt_date }}</span>
                </li>
                <li v-if="exam.open_at" class="flex justify-between">
                    <span>يفتح في:</span>
                    <span class="font-semibold">{{
                        formatDate(exam.open_at)
                    }}</span>
                </li>
                <li v-if="exam.close_at" class="flex justify-between">
                    <span>يغلق في:</span>
                    <span class="font-semibold">{{
                        formatDate(exam.close_at)
                    }}</span>
                </li>
            </ul>

            <div class="mt-6 text-center">
                <Alert :type="alertInfo.type" :message="alertInfo.message" />

                <BaseButton
                    @click="showConfirm = true"
                    :disabled="!can_start"
                    :class="{
                        '!cursor-not-allowed': !can_start,
                    }"
                >
                    بدء الاختبار
                </BaseButton>

                <div v-if="attempts_count > 0" class="mt-4">
                    <Link
                        :href="route('attempts.index')"
                        class="text-sm text-blue-500 hover:underline"
                    >
                        عرض محاولاتك السابقة
                    </Link>
                </div>
            </div>
        </Card>

        <ConfirmDialog
            :show="showConfirm"
            title="التعهد قبل بدء الاختبار"
            confirm-txt="أتعهد"
            @confirm="startExam"
            @cancel="showConfirm = false"
        >
            <p
                class="rounded-lg border-4 border-red-600 bg-red-50/50 p-4 text-right text-lg font-extrabold text-red-700 shadow-lg dark:bg-red-900/50 dark:text-red-400"
            >
                <span class="text-xl">
                    أعاهد الله تعالى أن لا أغش في هذا الامتحان وأن لا أصور
                    الشاشة.
                </span>
                <br /><br />
                <span>
                    وأن أقدم هذا الأمتحان بكل أمانة، وأن لا أنقل معلومات هذا
                    الامتحان لأحد أبداً
                </span>
            </p>
        </ConfirmDialog>
    </div>
</template>
