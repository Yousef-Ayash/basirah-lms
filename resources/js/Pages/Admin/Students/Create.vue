<template>
    <div>
        <!-- <Head title="إنشاء طالب جديد" />
        <h1 class="mb-4 text-2xl font-bold">إنشاء طالب جديد</h1> -->

        <PageHeader title="إنشاء طالب جديد" />

        <form @submit.prevent="submit">
            <StudentForm v-model="form" :levels="levels" />
            <div class="mt-4 flex justify-between">
                <BaseButton
                    as="a"
                    :href="route('admin.students.index')"
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

<script setup>
import AdminLayout from '@/Pages/Admin/Layout.vue';
import BaseButton from '@/components/FormElements/BaseButton.vue';
import PageHeader from '@/components/LayoutStructure/PageHeader.vue';
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
