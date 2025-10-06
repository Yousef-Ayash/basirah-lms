<template>
    <div class="flex min-h-screen items-center justify-center bg-gray-50 p-4 dark:bg-gray-900">
        <Head title="تسجيل" />
        <div class="w-full max-w-md">
            <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                <h1 class="mb-4 text-2xl font-semibold text-[#61CE70]">إنشاء حساب</h1>

                <form @submit.prevent="submit" class="space-y-4">
                    <BaseInput label="الاسم الكامل" v-model="form.name" :error="form.errors.name" required autocomplete="name" />
                    <BaseInput
                        label="البريد الإلكتروني"
                        type="email"
                        v-model="form.email"
                        :error="form.errors.email"
                        required
                        autocomplete="username"
                    />
                    <BaseInput
                        label="كلمة المرور"
                        type="password"
                        v-model="form.password"
                        :error="form.errors.password"
                        required
                        autocomplete="new-password"
                    />
                    <BaseInput
                        label="تأكيد كلمة المرور"
                        type="password"
                        v-model="form.password_confirmation"
                        :error="form.errors.password_confirmation"
                        required
                        autocomplete="new-password"
                    />

                    <div class="flex items-center justify-between">
                        <button
                            class="rounded bg-[#61CE70] px-4 py-2 font-medium text-white disabled:opacity-60"
                            type="submit"
                            :disabled="form.processing"
                        >
                            <span v-if="form.processing">جارٍ إنشاء الحساب...</span>
                            <span v-else>إنشاء حساب</span>
                        </button>

                        <Link :href="route('login')" class="text-sm text-gray-600 hover:underline dark:text-gray-300">
                            لديك حساب بالفعل؟
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import BaseInput from '@/components/FormElements/BaseInput.vue';

import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    // The second argument to .post() is an object of options
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>
