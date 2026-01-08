<template>
    <div>
        <Head title="إنشاء مادة جديدة" />
        <h1 class="mb-4 text-2xl font-bold">إنشاء مادة جديدة</h1>

        <form @submit.prevent="storeSubject" enctype="multipart/form-data">
            <!-- SubjectForm expects v-model="form" and will emit update:modelValue -->
            <SubjectForm
                :modelValue="form"
                :levels="levels"
                :teachers="teachers"
                :courses="courses"
                @update:modelValue="(partial) => Object.assign(form, partial)"
            />

            <div class="mt-4 flex justify-between">
                <BaseButton
                    as="a"
                    :href="route('admin.subjects.index')"
                    class="bg-red-500 text-white hover:bg-red-600"
                >
                    إلغاء
                </BaseButton>
                <BaseButton type="submit" :disabled="form.processing">
                    إنشاء
                </BaseButton>
            </div>
        </form>
    </div>
</template>

<script setup>
import AdminLayout from '@/Pages/Admin/Layout.vue';
import BaseButton from '@/components/FormElements/BaseButton.vue';
import SubjectForm from '@/components/Subjects/SubjectForm.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    levels: Array,
    teachers: Array,
    courses: Array,
});

const form = useForm({
    title: '',
    description: '',
    level_id: 0,
    teacher_id: 0,
    course: 0,
    cover: null,
});

function storeSubject() {
    // send as multipart automatically if any value is a File
    form.post(route('admin.subjects.store'), {
        onError: (errs) => {
            // debug errors in console
            // eslint-disable-next-line no-console
            console.log('validation errors', errs);
        },
        onSuccess: () => {
            // optional success handling
        },
    });
}

import { nextTick } from 'vue';

async function submit() {
    await nextTick(); // allow any last emissions to flush
    form.post(route('admin.subjects.store'));
}
</script>
