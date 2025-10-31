<template>
    <Modal :show="show" @close="close">
        <div class="sticky top-0 z-10 border-b bg-white p-4 dark:bg-gray-800">
            <h2 class="text-xl font-bold">
                {{ `إضافة أسئلة إلى: ${exam?.title}` }}
            </h2>
        </div>

        <div class="max-h-[60vh] space-y-6 overflow-y-auto p-4">
            <div>
                <label class="mb-1 block text-sm font-medium"
                    >📄 رفع من ملف إكسل</label
                >
                <div
                    @click="fileInput.click()"
                    class="cursor-pointer rounded-md border-2 border-dashed p-6 text-center hover:border-blue-500"
                >
                    <p>انقر لاختيار ملف إكسل (.xlsx)</p>
                    <input
                        type="file"
                        ref="fileInput"
                        class="hidden"
                        accept=".xlsx,.xls,.csv"
                        @change="handleFileSelect"
                    />
                </div>
                <p
                    v-if="excelImportForm.errors.file"
                    class="mt-1 text-sm text-red-500"
                >
                    {{ excelImportForm.errors.file }}
                </p>
                <p class="mt-1 text-xs text-gray-500">
                    سينقلك هذا إلى شاشة معاينة لتأكيد الاستيراد.
                </p>
            </div>

            <p class="text-center font-semibold">أو</p>

            <Card class="space-y-4">
                <h3 class="font-semibold">إضافة سؤال يدويًا</h3>
                <BaseTextarea
                    label="نص السؤال"
                    v-model="questionForm.question_text"
                    :error="questionForm.errors.question_text"
                />
                <BaseInput
                    label="عدد الخيارات"
                    :type="'number'"
                    min="2"
                    v-model.number="choiceCount"
                    @input="adjustChoices"
                />

                <div v-for="(opt, i) in questionForm.options" :key="i">
                    <BaseInput
                        :label="`الخيار ${i + 1}`"
                        v-model="questionForm.options[i]"
                        :error="questionForm.errors[`options.${i}`]"
                    />
                </div>

                <BaseSelect
                    label="الإجابة الصحيحة"
                    v-model="questionForm.correct_answer"
                    :error="questionForm.errors.correct_answer"
                >
                    <option :value="null">اختر الإجابة الصحيحة</option>
                    <option
                        v-for="(opt, i) in questionForm.options"
                        :key="i"
                        :value="i + 1"
                    >
                        {{
                            questionForm.options[i] !== ''
                                ? questionForm.options[i]
                                : `الخيار ${i + 1} | اكتب الخيار`
                        }}
                    </option>
                </BaseSelect>

                <BaseInput
                    label="علامة السؤال"
                    type="number"
                    step="0.1"
                    v-model="questionForm.mark"
                    :error="questionForm.errors.mark"
                    placeholder="مثال: 4"
                />

                <BaseButton @click="stageQuestion">
                    + إضافة إلى الدفعة
                </BaseButton>
            </Card>

            <div v-if="stagedQuestions.length > 0">
                <h3 class="mb-2 font-semibold">
                    {{ `أسئلة جديدة ستتم إضافتها (${stagedQuestions.length})` }}
                </h3>
                <Card>
                    <ul class="divide-y dark:divide-gray-700">
                        <li
                            v-for="(question, index) in stagedQuestions"
                            :key="index"
                            class="p-2 text-sm"
                        >
                            {{ question.question_text }}
                        </li>
                    </ul>
                </Card>
            </div>
        </div>

        <div
            class="sticky bottom-0 z-10 border-t bg-white p-4 dark:bg-gray-800"
        >
            <BaseButton class="w-full" @click="submitStagedQuestions">
                ✅ حفظ وإغلاق
            </BaseButton>
        </div>
    </Modal>
</template>

<script setup>
import BaseButton from '@/components/FormElements/BaseButton.vue';
import BaseInput from '@/components/FormElements/BaseInput.vue';
import BaseSelect from '@/components/FormElements/BaseSelect.vue';
import BaseTextarea from '@/components/FormElements/BaseTextarea.vue';
import Card from '@/components/LayoutStructure/Card.vue';
import Modal from '@/components/LayoutStructure/Modal.vue';

import { router, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    show: Boolean,
    exam: Object,
});

const emit = defineEmits(['close']);

const stagedQuestions = ref([]);
const choiceCount = ref(2);

const questionForm = useForm({
    question_text: '',
    options: ['', ''],
    correct_answer: null,
    mark: 2.5,
});

const excelImportForm = useForm({ file: null });
const fileInput = ref(null);

watch(
    () => props.show,
    (isVisible) => {
        if (!isVisible) {
            stagedQuestions.value = [];
            questionForm.reset();
        }
    },
);

const adjustChoices = () => {
    const count = Math.max(2, choiceCount.value || 0);
    const currentOptions = questionForm.options;

    if (count > currentOptions.length) {
        for (let i = currentOptions.length; i < count; i++) {
            currentOptions.push('');
        }
    } else if (count < currentOptions.length) {
        questionForm.options.splice(count);
    }
};

const stageQuestion = () => {
    stagedQuestions.value.push(questionForm.data());
    questionForm.reset();
    adjustChoices();
};

const submitStagedQuestions = () => {
    if (stagedQuestions.value.length === 0) {
        return close();
    }
    router.post(
        route('admin.exams.questions.store.batch', { exam: props.exam.id }),
        {
            questions: stagedQuestions.value,
        },
        {
            onSuccess: () => close(),
            preserveScroll: true,
        },
    );
};

const close = () => {
    emit('close');
};

const handleFileSelect = (event) => {
    const file = event.target.files[0];
    if (!file) return;
    excelImportForm.file = file;
    router.post(
        route('admin.exams.questions.import.preview', { exam: props.exam.id }),
        {
            _method: 'post',
            file: excelImportForm.file,
        },
        {
            forceFormData: true,
        },
    );
};
</script>
