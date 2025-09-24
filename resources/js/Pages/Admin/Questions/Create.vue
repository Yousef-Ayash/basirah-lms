<template>
    <div>
        <Head :title="__('admin.add_questions_to', { title: exam.title })" />
        <h1 class="mb-4 text-2xl font-bold">{{ __('admin.add_questions_to', { title: exam.title }) }}</h1>
        <form @submit.prevent="submit">
            <Card class="space-y-4">
                <BaseTextarea :label="__('labels.question_text')" v-model="form.question_text" :error="form.errors.question_text" required rows="4" />
                <BaseInput :label="__('labels.number_of_choices')" type="number" min="2" v-model.number="choiceCount" @input="adjustChoices" />

                <div v-for="(option, index) in form.options" :key="index">
                    <BaseInput
                        :label="__('labels.option_number', { number: index + 1 })"
                        v-model="form.options[index]"
                        :error="form.errors[`options.${index}`]"
                    />
                </div>

                <BaseSelect :label="__('correct_answer')" v-model="form.correct_answer" :error="form.errors.correct_answer" required>
                    <option :value="null">Select the correct option</option>
                    <option v-for="(option, index) in form.options" :key="index" :value="index + 1">
                        {{ __('labels.option_number', { number: index + 1 }) }}
                    </option>
                </BaseSelect>
            </Card>
            <div class="mt-4 flex justify-end">
                <BaseButton type="submit" :disabled="form.processing">{{ __('buttons.save_question') }}</BaseButton>
            </div>
        </form>
    </div>
</template>

<script setup>
import AdminLayout from '@/Pages/Admin/Layout.vue';
import BaseButton from '@/components/FormElements/BaseButton.vue';
import BaseInput from '@/components/FormElements/BaseInput.vue';
import BaseSelect from '@/components/FormElements/BaseSelect.vue';
import BaseTextarea from '@/components/FormElements/BaseTextarea.vue';
import Card from '@/components/LayoutStructure/Card.vue';
import { useTranslations } from '@/composables/useTranslations';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const { __ } = useTranslations();

defineOptions({ layout: AdminLayout });

const props = defineProps({
    exam: Object,
});

const choiceCount = ref(2);
const form = useForm({
    question_text: '',
    options: ['', ''],
    correct_answer: null, // Stored as 1-based index
});

const adjustChoices = () => {
    // This line now only enforces a minimum of 3
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

const submit = () => {
    form.post(route('admin.exams.questions.store', { exam: props.exam.id }));
};
</script>
