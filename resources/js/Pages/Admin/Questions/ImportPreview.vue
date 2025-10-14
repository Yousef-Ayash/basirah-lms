<template>
    <div>
        <Head :title="`معاينة الاستيراد لـ ${exam.title}`" />
        <SectionHeader :title="`استيراد أسئلة لـ ${exam.title}`" />
        <p class="mb-4">
            راجع الأسئلة المستخرجة من ملفك. إذا كانت هناك أخطاء، يرجى تصحيح ملفك
            وإعادة تحميله.
        </p>

        <Alert
            v-if="hasErrors"
            type="error"
            message="يحتوي ملفك على أخطاء (محددة باللون الأحمر). يجب عليك اختيار 'تخطي' أو 'تحديث' التكرارات للمتابعة."
        />
        <Alert
            v-else
            type="success"
            message="لم يتم العثور على أخطاء. راجع البيانات أدناه وانقر على 'تأكيد الاستيراد' للإنهاء."
        />

        <Card class="mt-4">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-100 text-right dark:bg-gray-700">
                            <th class="p-2">صف</th>
                            <th class="p-2">نص السؤال</th>
                            <th class="p-2">الخيارات</th>
                            <th class="p-2">صحيح</th>
                            <th class="p-2">علامة السؤال</th>
                            <th class="p-2">الأخطاء</th>
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
                                <span v-else class="text-green-500"
                                    >✓ جاهز</span
                                >
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
                >إلغاء</BaseButton
            >
            <BaseButton
                @click="submit"
                :disabled="hasErrors || form.processing"
            >
                تأكيد الاستيراد
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

import { Head, useForm } from '@inertiajs/vue3';

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
