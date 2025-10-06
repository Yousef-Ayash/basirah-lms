<template>
    <div>
        <Head title="تعديل سؤال" />
        <h1 class="mb-4 text-2xl font-bold">
            {{ `تعديل سؤال لـ: ${exam.title}` }}
        </h1>
        <form @submit.prevent="submit">
            <Card class="space-y-4">
                <BaseTextarea
                    label="نص السؤال"
                    v-model="form.question_text"
                    :error="form.errors.question_text"
                    required
                    rows="4"
                />
                <p class="text-sm">
                    لا يمكن تغيير عدد الخيارات أثناء التعديل.
                </p>
                <div v-for="(option, index) in form.options" :key="index">
                    <BaseInput
                        :label="
                            `الخيار ${index + 1}`
                        "
                        v-model="form.options[index]"
                        :error="form.errors[`options.${index}`]"
                    />
                </div>
                <BaseSelect
                    label="الإجابة الصحيحة"
                    v-model="form.correct_answer"
                    :error="form.errors.correct_answer"
                    required
                >
                    <option
                        v-for="(option, index) in form.options"
                        :key="index"
                        :value="index + 1"
                    >
                        {{ `الخيار ${index + 1}` }}
                    </option>
                </BaseSelect>
                <BaseInput
                    label="علامة السؤال"
                    type="number"
                    v-model="form.mark"
                    :error="form.errors.mark"
                    placeholder="مثال: 4"
                />
            </Card>
            <div class="mt-4 flex justify-end">
                <BaseButton type="submit" :disabled="form.processing">حفظ التغييرات</BaseButton>
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

import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

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
    mark: props.question.mark,
});

const submit = () => {
    form.put(
        route('admin.exams.questions.update', {
            exam: props.exam.id,
            question: props.question.id,
        }),
    );
};
</script>
