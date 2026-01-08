<template>
    <div>
        <Head :title="`تعديل المقرر: ${course.title}`" />
        <h1 class="mb-4 text-2xl font-bold">
            {{ `تعديل المقرر: ${course.title}` }}
        </h1>

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
                    حفظ التغييرات
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

const props = defineProps({
    course: Object,
});

const form = useForm({
    _method: 'PUT', // Spoofing PUT for file upload support in Laravel
    title: props.course.title || '',
    description: props.course.description || '',
    order: props.course.order,
    cover: null,
    cover_url: props.course.cover_url || null,
    remove_cover: false,
});

const submit = () => {
    // Inertia automatically converts to FormData when files are present,
    // but explicit forceFormData ensures behavior consistency.
    form.post(route('admin.courses.update', props.course.id), {
        forceFormData: true,
    });
};
</script>
