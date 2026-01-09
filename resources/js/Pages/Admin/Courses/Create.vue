<template>
    <div>
        <Head title="إنشاء مقرر جديد" />
        <h1 class="mb-4 text-2xl font-bold">إنشاء مقرر جديد</h1>

        <form @submit.prevent="submit" enctype="multipart/form-data">
            <CourseForm
                :modelValue="form"
                @update:modelValue="(partial) => Object.assign(form, partial)"
            />

            <div class="mt-4 flex justify-between">
                <BaseButton
                    as="a"
                    :href="route('admin.courses.index')"
                    class="bg-red-500 text-white hover:bg-red-600"
                >
                    إلغاء
                </BaseButton>
                <BaseButton type="submit" :disabled="form.processing">
                    إنشاء المقرر
                </BaseButton>
            </div>
        </form>
    </div>
</template>

<script setup>
import AdminLayout from '@/Pages/Admin/Layout.vue';
import BaseButton from '@/components/FormElements/BaseButton.vue';
import CourseForm from '@/components/Courses/CourseForm.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineOptions({ layout: AdminLayout });

const form = useForm({
    title: '',
    description: '',
    order: '',
    cover: null,
});

const submit = () => {
    form.post(route('admin.courses.store'), {
        forceFormData: true,
    });
};
</script>
