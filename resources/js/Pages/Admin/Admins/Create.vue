<script setup>
import AdminLayout from '@/Pages/Admin/Layout.vue';
import BaseButton from '@/components/FormElements/BaseButton.vue';
import AdminForm from '@/components/Admins/AdminForm.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineOptions({ layout: AdminLayout });

const form = useForm({
    name: '',
    email: '',
    phone: '',
    password: '',
});

const submit = () => {
    form.post(route('admin.admins.store'));
};
</script>

<template>
    <div>
        <Head title="إنشاء مسؤول جديد" />
        <h1 class="mb-4 text-2xl font-bold">إنشاء مسؤول جديد</h1>
        <form @submit.prevent="submit">
            <AdminForm v-model="form" :isEdit="false" />
            <div class="mt-4 flex justify-between">
                <BaseButton
                    as="a"
                    :href="route('admin.admins.index')"
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
