<script setup>
import AdminLayout from '@/Pages/Admin/Layout.vue';
import BaseButton from '@/components/FormElements/BaseButton.vue';
import BaseFileInput from '@/components/FormElements/BaseFileInput.vue';
import Card from '@/components/LayoutStructure/Card.vue';
import SectionHeader from '@/components/LayoutStructure/SectionHeader.vue';

import { Head, useForm } from '@inertiajs/vue3';

defineOptions({ layout: AdminLayout });

const form = useForm({
    file: null,
});

const submit = () => {
    form.post(route('admin.marks.import.preview'));
};
</script>

<template>
    <div>
        <Head title="استيراد الدرجات" />
        <SectionHeader title="استيراد الدرجات" />

        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            <div class="md:col-span-1">
                <Card>
                    <h3 class="mb-2 font-semibold">تعليمات</h3>
                    <p class="mb-2 text-sm text-gray-600 dark:text-gray-400">قم بتحميل ملف Excel (.xlsx) أو CSV بالأعمدة التالية:</p>
                    <ul class="list-inside list-disc space-y-1 text-sm">
                        <li>**user_identifier** (مطلوب: بريد الطالب الإلكتروني أو معرفه)</li>
                        <li>**exam_identifier** (مطلوب: عنوان الاختبار أو معرفه)</li>
                        <li>**marks** (مطلوب: رقم من 0 إلى 100)</li>
                        <li>**notes** (اختياري)</li>
                    </ul>
                </Card>
            </div>

            <div class="md:col-span-2">
                <form @submit.prevent="submit">
                    <Card class="space-y-4">
                        <BaseFileInput
                            label="ملف بيانات الدرجات"
                            hint="حجم الملف الأقصى: 50 ميجابايت. الصيغ المدعومة: .xlsx, .xls, .csv"
                            v-model="form.file"
                            :error="form.errors.file"
                            accept=".xlsx,.xls,.csv"
                            required
                        />
                        <div class="flex justify-end">
                            <BaseButton type="submit" :disabled="form.processing"> رفع ومعاينة </BaseButton>
                        </div>
                    </Card>
                </form>
            </div>
        </div>
    </div>
</template>
