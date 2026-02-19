<script setup>
import AdminLayout from '@/Pages/Admin/Layout.vue';
import BaseButton from '@/components/FormElements/BaseButton.vue';
import AdminForm from '@/components/Admins/AdminForm.vue';
import { Head, useForm } from '@inertiajs/vue3';
import PageHeader from '@/components/LayoutStructure/PageHeader.vue';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    admin: Object,
});

const form = useForm({
    name: props.admin.name,
    email: props.admin.email,
    phone: props.admin.phone,
    password: '',
});

const submit = () => {
    form.put(route('admin.admins.update', props.admin.id));
};
</script>

<template>
    <div>
        <!-- <Head :title="`تعديل المسؤول: ${admin.name}`" />
        <h1 class="mb-4 text-2xl font-bold">تعديل بيانات المسؤول</h1> -->

        <PageHeader :title="`تعديل المسؤول: ${admin.name}`" />

        <form @submit.prevent="submit">
            <AdminForm v-model="form" :isEdit="true" />
            <div class="mt-4 flex justify-between">
                <BaseButton
                    as="a"
                    :href="route('admin.admins.index')"
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
