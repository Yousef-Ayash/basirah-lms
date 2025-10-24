<template>
    <div>
        <Head title="إنشاء طالب جديد" />
        <h1 class="mb-4 text-2xl font-bold">إنشاء طالب جديد</h1>
        <form @submit.prevent="submit">
            <StudentForm v-model="form" :levels="levels" />
            <div class="mt-4 flex justify-between">
                <div class="justify-start">
                    <BaseButton
                        as="a"
                        :href="route('admin.students.index')"
                        class="bg-red-500 text-white hover:bg-red-600"
                    >
                        إلغاء
                    </BaseButton>
                </div>
                <div class="justify-end">
                    <BaseButton type="submit" :disabled="form.processing"
                        >إنشاء</BaseButton
                    >
                </div>
            </div>
        </form>
    </div>
</template>

<script setup>
import AdminLayout from '@/Pages/Admin/Layout.vue';
import BaseButton from '@/components/FormElements/BaseButton.vue';
import StudentForm from '@/components/Students/StudentForm.vue';

import { Head, useForm } from '@inertiajs/vue3';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    levels: Array,
});

const form = useForm({
    name: '',
    // email: '',
    phone: '',
    password: '',
    level_id: null,
    is_approved: true,
});

const submit = () => {
    form.post(route('admin.students.store'));
};
</script>
