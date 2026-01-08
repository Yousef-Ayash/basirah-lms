<script setup>
import AdminLayout from '@/Pages/Admin/Layout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import TeacherForm from '@/components/Teachers/TeacherForm.vue';
import BaseButton from '@/components/FormElements/BaseButton.vue';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    teacher: Object,
});

const form = useForm({
    _method: 'PUT',
    name: props.teacher.name,
    order: props.teacher.order,
    bio: props.teacher.bio,
    photo: null,
    photo_url: props.teacher.photo_url || null,
    remove_photo: false,
});

const submit = () => {
    form.post(route('admin.teachers.update', props.teacher.id), {
        forceFormData: true,
    });
};
</script>

<template>
    <div>
        <!-- <Head :title="'Edit Teacher: ' + teacher.name" /> -->
        <Head :title="`تعديل بيانات المدرس: ${teacher.name}`" />
        <h1 class="mb-4 text-2xl font-bold">تعديل بيانات المدرس</h1>
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
                    >حفظ التغييرات</BaseButton
                >
            </div>
        </form>
    </div>
</template>
