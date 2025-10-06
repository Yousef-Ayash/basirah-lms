<script setup>
import AdminLayout from '@/Pages/Admin/Layout.vue';
import BaseButton from '@/components/FormElements/BaseButton.vue';
import BaseInput from '@/components/FormElements/BaseInput.vue';
import BaseSelect from '@/components/FormElements/BaseSelect.vue';
import Card from '@/components/LayoutStructure/Card.vue';
import SectionHeader from '@/components/LayoutStructure/SectionHeader.vue';

import { Head, router } from '@inertiajs/vue3';
import axios from 'axios';
import { reactive, ref, watch } from 'vue';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    users: Array,
    subjects: Array,
    levels: Array,
});

const studentExamForm = reactive({
    user_id: null,
    exam_id: null,
});

const studentSpecificExams = ref([]);
const isLoadingExams = ref(false);

watch(
    () => studentExamForm.user_id,
    (newUserId) => {
        studentExamForm.exam_id = null;
        studentSpecificExams.value = [];

        if (newUserId) {
            isLoadingExams.value = true;
            axios
                .get(route('admin.reports.exams_for_student', { user: newUserId }))
                .then((response) => {
                    studentSpecificExams.value = response.data;
                })
                .finally(() => {
                    isLoadingExams.value = false;
                });
        }
    },
);

const subjectAggregateForm = reactive({
    subject_id: null,
    pass_threshold: 50,
});

const levelSummaryForm = reactive({
    level_id: null,
    pass_threshold: 50,
});

const generateStudentExamReport = () => {
    router.get(route('admin.reports.student_exam'), studentExamForm);
};

const generateSubjectReport = () => {
    router.get(route('admin.reports.subject'), subjectAggregateForm);
};

const generateLevelReport = () => {
    router.get(route('admin.reports.level'), levelSummaryForm);
};
</script>

<template>
    <div>
        <Head title="التقارير" />
        <SectionHeader title="تقارير النظام" />
        <p class="mb-6 text-sm text-gray-600 dark:text-gray-400">إنشاء وعرض تقارير مفصلة عن أداء الطلاب والمحتوى.</p>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Card 1: Per Student Exam Report -->
            <Card class="space-y-4">
                <h3 class="text-lg font-semibold">تقرير اختبار الطالب</h3>
                <p class="text-xs text-gray-500">عرض جميع المحاولات والدرجات لطالب واحد في اختبار معين.</p>
                <form @submit.prevent="generateStudentExamReport">
                    <div class="space-y-2">
                        <BaseSelect v-model="studentExamForm.user_id" required>
                            <option :value="null">اختر طالباً</option>
                            <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                        </BaseSelect>
                        <BaseSelect
                            label="الاختبار"
                            v-model="studentExamForm.exam_id"
                            required
                            :disabled="!studentExamForm.user_id || isLoadingExams"
                        >
                            <option :value="null">اختر امتحان</option>
                            <option v-for="exam in studentSpecificExams" :key="exam.id" :value="exam.id">{{ exam.title }}</option>
                        </BaseSelect>
                        <BaseButton type="submit" class="w-full">إنشاء تقرير</BaseButton>
                    </div>
                </form>
            </Card>

            <!-- Card 2: Per Subject Aggregate Report -->
            <Card class="space-y-4">
                <h3 class="text-lg font-semibold">أداء المادة</h3>
                <p class="text-xs text-gray-500">شاهد الإحصائيات المجمعة (متوسط الدرجات، نسبة النجاح) لمادة معينة.</p>
                <form @submit.prevent="generateSubjectReport">
                    <div class="space-y-2">
                        <BaseSelect v-model="subjectAggregateForm.subject_id" required>
                            <option :value="null">اختر مادة</option>
                            <option v-for="subject in subjects" :key="subject.id" :value="subject.id">{{ subject.title }}</option>
                        </BaseSelect>
                        <BaseInput type="number" label="عتبة النجاح (%)" v-model="subjectAggregateForm.pass_threshold" />
                        <BaseButton type="submit" class="w-full">إنشاء تقرير</BaseButton>
                    </div>
                </form>
            </Card>

            <!-- Card 3: Per Level Summary Report -->
            <Card class="space-y-4">
                <h3 class="text-lg font-semibold">ملخص المستوى</h3>
                <p class="text-xs text-gray-500">احصل على ملخص عالي المستوى للأداء عبر جميع المواد في مستوى معين.</p>
                <form @submit.prevent="generateLevelReport">
                    <div class="space-y-2">
                        <BaseSelect v-model="levelSummaryForm.level_id" required>
                            <option :value="null">اختر مستوى</option>
                            <option v-for="level in levels" :key="level.id" :value="level.id">{{ level.name }}</option>
                        </BaseSelect>
                        <BaseInput type="number" label="عتبة النجاح (%)" v-model="levelSummaryForm.pass_threshold" />
                        <BaseButton type="submit" class="w-full">إنشاء تقرير</BaseButton>
                    </div>
                </form>
            </Card>
        </div>
    </div>
</template>
