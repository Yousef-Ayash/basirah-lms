<script setup>
import AdminLayout from '@/Pages/Admin/Layout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import TeacherForm from '@/components/Teachers/TeacherForm.vue';
import BaseButton from '@/components/FormElements/BaseButton.vue';

defineOptions({ layout: AdminLayout });

const form = useForm({
    name: '',
    order: 1,
    bio: '',
    photo: null,
});

const submit = () => {
    form.post(route('admin.teachers.store'));
};
</script>

<template>
    <div>
        <Head title="إنشاء مدرس" />
        <h1 class="mb-4 text-2xl font-bold">إنشاء مدرس جديد</h1>
        <form @submit.prevent="submit">
            <TeacherForm v-model="form" />
            <div class="mt-4 flex justify-between">
                <BaseButton
                    as="a"
                    :href="route('admin.teachers.index')"
                    class="bg-red-500 text-white hover:bg-red-600"
                >
                    إلغاء
                </BaseButton>
                <BaseButton type="submit" :disabled="form.processing"
                    >إنشاء</BaseButton
                >
            </div>
        </form>
    </div>
</template>
