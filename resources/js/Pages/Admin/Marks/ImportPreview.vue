<script setup>
import AdminLayout from '@/Pages/Admin/Layout.vue';
import BaseButton from '@/components/FormElements/BaseButton.vue';
import Card from '@/components/LayoutStructure/Card.vue';
import SectionHeader from '@/components/LayoutStructure/SectionHeader.vue';
import Alert from '@/components/Misc/Alert.vue';

import { Head, useForm } from '@inertiajs/vue3';
import { forEach } from 'lodash';
import { computed } from 'vue';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    preview: Array,
    preview_id: String,
});

props.preview.forEach((row) => {
    console.log(row);
});

const form = useForm({
    preview_id: props.preview_id,
});

const hasErrors = computed(() =>
    props.preview.some((row) => row.errors.length > 0),
);

const submit = () => {
    form.post(route('admin.marks.import.commit'));
};
</script>

<template>
    <div>
        <Head title="تأكيد استيراد الدرجات" />
        <SectionHeader title="تأكيد استيراد الدرجات" />
        <Alert
            v-if="hasErrors"
            type="error"
            message="يحتوي ملفك على أخطاء (محددة باللون الأحمر). يرجى تصحيح الملف وإعادة تحميله."
            class="mb-4"
        />
        <Alert
            v-else
            type="success"
            message="لم يتم العثور على أخطاء. راجع البيانات أدناه وانقر على 'تأكيد الاستيراد' للإنهاء."
            class="mb-4"
        />

        <Card>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 text-right dark:bg-gray-700">
                        <tr>
                            <th class="p-2">ID</th>
                            <th class="p-2">الطالب</th>
                            <th class="p-2">رقم الهاتف</th>
                            <th class="p-2">الاختبار</th>
                            <th class="p-2">الدرجات</th>
                            <th class="p-2">ملاحظات</th>
                            <th class="p-2">الحالة / الأخطاء</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="row in preview"
                            :key="row.row"
                            class="border-t dark:border-gray-700"
                        >
                            <td class="p-2">{{ row.user_id }}</td>
                            <td class="p-2">{{ row.user_name }}</td>
                            <td class="p-2">{{ row.user_phone }}</td>
                            <td class="p-2">{{ row.exam_title }}</td>
                            <td class="p-2">{{ row.marks }}</td>
                            <td class="p-2">{{ row.notes }}</td>
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
                :href="route('admin.marks.index')"
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
