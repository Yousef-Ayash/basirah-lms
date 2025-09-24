<template>
    <Modal :show="show" @close="close">
        <div class="sticky top-0 z-10 border-b bg-white p-4 dark:bg-gray-800">
            <h2 class="text-xl font-bold">{{ __('admin.add_questions_to', { title: exam?.title }) }}</h2>
        </div>

        <div class="max-h-[60vh] space-y-6 overflow-y-auto p-4">
            <div>
                <label class="mb-1 block text-sm font-medium">📄 {{ __('admin.upload_from_excel') }}</label>
                <div @click="fileInput.click()" class="cursor-pointer rounded-md border-2 border-dashed p-6 text-center hover:border-blue-500">
                    <p>{{ __('admin.click_to_select_excel') }}</p>
                    <input type="file" ref="fileInput" class="hidden" accept=".xlsx,.xls,.csv" @change="handleFileSelect" />
                </div>
                <p v-if="excelImportForm.errors.file" class="mt-1 text-sm text-red-500">{{ excelImportForm.errors.file }}</p>
                <p class="mt-1 text-xs text-gray-500">{{ __('admin.import_preview_info') }}</p>
            </div>

            <p class="text-center font-semibold">{{ __('common.or') }}</p>

            <Card class="space-y-4">
                <h3 class="font-semibold">{{ __('admin.add_question_manually') }}</h3>
                <BaseTextarea :label="__('labels.question_text')" v-model="questionForm.question_text" :error="questionForm.errors.question_text" />
                <BaseInput :label="__('labels.number_of_choices')" :type="'number'" min="2" v-model.number="choiceCount" @input="adjustChoices" />

                <div v-for="(opt, i) in questionForm.options" :key="i">
                    <BaseInput
                        :label="__('labels.option_number', { number: i + 1 })"
                        v-model="questionForm.options[i]"
                        :error="questionForm.errors[`options.${i}`]"
                    />
                </div>

                <BaseSelect :label="__('labels.correct_answer')" v-model="questionForm.correct_answer" :error="questionForm.errors.correct_answer">
                    <option :value="null">{{ __('labels.select_correct_option') }}</option>
                    <option v-for="(opt, i) in questionForm.options" :key="i" :value="i + 1">
                        {{ __('labels.option_number', { number: i + 1 }) }}
                    </option>
                </BaseSelect>

                <BaseButton @click="stageQuestion"> {{ __('buttons.add_to_batch') }} </BaseButton>
            </Card>

            <div v-if="stagedQuestions.length > 0">
                <h3 class="mb-2 font-semibold">{{ __('admin.new_questions_batch', { count: stagedQuestions.length }) }}</h3>
                <Card>
                    <ul class="divide-y dark:divide-gray-700">
                        <li v-for="(question, index) in stagedQuestions" :key="index" class="p-2 text-sm">
                            {{ question.question_text }}
                        </li>
                    </ul>
                </Card>
            </div>
        </div>

        <div class="sticky bottom-0 z-10 border-t bg-white p-4 dark:bg-gray-800">
            <BaseButton class="w-full" @click="submitStagedQuestions"> {{ __('buttons.save_and_close') }} </BaseButton>
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
import { useTranslations } from '@/composables/useTranslations';
import { router, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const { __ } = useTranslations();

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
