<template>
    <div>
        <Head :title="__('admin.import_preview_for', { title: exam.title })" />
        <SectionHeader
            :title="__('admin.import_questions_for', { title: exam.title })"
        />
        <p class="mb-4">{{ __('admin.import_preview_instructions') }}</p>

        <Alert
            v-if="hasErrors"
            type="error"
            :message="__('messages.import_preview_error')"
        />
        <Alert
            v-else
            type="success"
            :message="__('messages.import_preview_success')"
        />

        <Card class="mt-4">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-100 text-start dark:bg-gray-700">
                            <th class="p-2">{{ __('labels.row_number') }}</th>
                            <th class="p-2">
                                {{ __('labels.question_text') }}
                            </th>
                            <th class="p-2">{{ __('labels.options') }}</th>
                            <th class="p-2">{{ __('labels.correct') }}</th>
                            <th class="p-2">علامة السؤال</th>
                            <th class="p-2">{{ __('common.errors') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="row in preview"
                            :key="row.row"
                            class="border-t dark:border-gray-700"
                        >
                            <td class="p-2">{{ row.row }}</td>
                            <td class="p-2">{{ row.question_text }}</td>
                            <td class="p-2">
                                <ul class="list-inside list-disc">
                                    <li
                                        v-for="(option, index) in row.options"
                                        :key="index"
                                    >
                                        {{ option }}
                                    </li>
                                </ul>
                            </td>
                            <td class="p-2">{{ row.correct_answer }}</td>
                            <td class="p-2">{{ row.mark }}</td>
                            <td class="p-2">
                                <ul
                                    v-if="row.errors.length"
                                    class="text-red-500"
                                >
                                    <li
                                        v-for="(error, i) in row.errors"
                                        :key="i"
                                    >
                                        {{ error }}
                                    </li>
                                </ul>
                                <span v-else class="text-green-500">{{
                                    __('messages.ready')
                                }}</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </Card>

        <div class="mt-6 flex justify-end gap-4">
            <BaseButton
                as="a"
                :href="route('admin.exams.show', exam.id)"
                class="bg-gray-500 hover:bg-gray-600"
                >{{ __('common.cancel') }}</BaseButton
            >
            <BaseButton
                @click="submit"
                :disabled="hasErrors || form.processing"
            >
                {{ __('buttons.commit_import') }}
            </BaseButton>
        </div>
    </div>
</template>

<script setup>
import AdminLayout from '@/Pages/Admin/Layout.vue';
import BaseButton from '@/components/FormElements/BaseButton.vue';
import Card from '@/components/LayoutStructure/Card.vue';
import SectionHeader from '@/components/LayoutStructure/SectionHeader.vue';
import Alert from '@/components/Misc/Alert.vue';
import { useTranslations } from '@/composables/useTranslations';
import { Head, useForm } from '@inertiajs/vue3';

const { __ } = useTranslations();

defineOptions({ layout: AdminLayout });

const props = defineProps({
    exam: Object,
    preview: Array,
    preview_id: String,
});

const form = useForm({
    preview_id: props.preview_id,
});

const hasErrors = props.preview.some((row) => row.errors.length > 0);

const submit = () => {
    form.post(
        route('admin.exams.questions.import.commit', { exam: props.exam.id }),
    );
};
</script>
