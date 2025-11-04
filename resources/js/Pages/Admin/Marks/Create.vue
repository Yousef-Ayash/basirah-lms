<script setup>
import AdminLayout from '@/Pages/Admin/Layout.vue';
import BaseButton from '@/components/FormElements/BaseButton.vue';
import MarkForm from '@/components/Marks/MarkForm.vue';

import { Head, useForm } from '@inertiajs/vue3';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    users: Array,
    exams: Array,
});

const form = useForm({
    user_id: null,
    exam_id: null,
    marks: '',
    notes: '',
});

const submit = () => {
    form.post(route('admin.marks.store'));
};
</script>

<template>
    <div>
        <Head title="إضافة درجة" />
        <h1 class="mb-4 text-2xl font-bold">إضافة درجة جديدة</h1>
        <form @submit.prevent="submit">
            <MarkForm v-model="form" :users="users" :exams="exams" />
            <div class="mt-4 flex justify-between">
                <BaseButton
                    as="a"
                    :href="route('admin.marks.index')"
                    class="bg-red-500 text-white hover:bg-red-600"
                >
                    إلغاء
                </BaseButton>
                <BaseButton type="submit" :disabled="form.processing"
                    >حفظ الدرجة</BaseButton
                >
            </div>
        </form>
    </div>
</template>
