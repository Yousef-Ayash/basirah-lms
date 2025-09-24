<template>
    <div>
        <Head :title="__('admin.edit_question')" />
        <h1 class="mb-4 text-2xl font-bold">{{ __('admin.edit_question_for', { title: exam.title }) }}</h1>
        <form @submit.prevent="submit">
            <Card class="space-y-4">
                <BaseTextarea :label="__('labels.question_text')" v-model="form.question_text" :error="form.errors.question_text" required rows="4" />
                <p class="text-sm">{{ __('admin.choices_cannot_be_changed') }}</p>
                <div v-for="(option, index) in form.options" :key="index">
                    <BaseInput
                        :label="__('labels.option_number', { number: index + 1 })"
                        v-model="form.options[index]"
                        :error="form.errors[`options.${index}`]"
                    />
                </div>
                <BaseSelect :label="__('labels.correct_answer')" v-model="form.correct_answer" :error="form.errors.correct_answer" required>
                    <option v-for="(option, index) in form.options" :key="index" :value="index + 1">
                        {{ __('labels.option_number', { number: index + 1 }) }}
                    </option>
                </BaseSelect>
            </Card>
            <div class="mt-4 flex justify-end">
                <BaseButton type="submit" :disabled="form.processing">{{ __('buttons.save_changes') }}</BaseButton>
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
    question: Object,
});

const choiceCount = ref(props.question.options.length);

const form = useForm({
    question_text: props.question.question_text,
    options: props.question.options,
    correct_answer: props.question.correct_answer,
});

const submit = () => {
    form.put(route('admin.exams.questions.update', { exam: props.exam.id, question: props.question.id }));
};
</script>
