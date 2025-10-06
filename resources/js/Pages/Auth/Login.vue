<template>
    <div
        class="flex min-h-screen items-center justify-center bg-gray-50 p-4 dark:bg-gray-900"
    >
        <Head title="تسجيل الدخول" />
        <div class="w-full max-w-md">
            <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                <h1 class="mb-4 text-2xl font-semibold text-[#61CE70]">
                    تسجيل الدخول
                </h1>

                <div
                    v-if="
                        form.hasErrors &&
                        !form.errors.email &&
                        !form.errors.password
                    "
                    class="mb-4 text-sm text-red-500"
                >
                    حدث خطأ في الإرسال.
                </div>

                <form @submit.prevent="submit" class="space-y-4">
                    <BaseInput
                        label="البريد الإلكتروني"
                        v-model="form.email"
                        :error="form.errors.email"
                        placeholder="you@example.com"
                        required
                        autocomplete="username"
                    />

                    <BaseInput
                        label="كلمة المرور"
                        type="password"
                        v-model="form.password"
                        :error="form.errors.password"
                        placeholder="••••••••"
                        required
                        autocomplete="current-password"
                    />

                    <div class="flex items-center justify-between">
                        <button
                            type="submit"
                            class="rounded bg-[#61CE70] px-4 py-2 font-medium text-white hover:cursor-pointer disabled:opacity-60"
                            :disabled="form.processing"
                        >
                            <span v-if="form.processing">جارٍ تسجيل الدخول...</span>
                            <span v-else>تسجيل الدخول</span>
                        </button>

                        <Link
                            :href="route('password.request')"
                            class="text-sm text-gray-600 hover:underline dark:text-gray-300"
                        >
                            هل نسيت كلمة المرور؟
                        </Link>
                    </div>
                </form>
                <p
                    class="mt-4 text-center text-sm text-gray-600 dark:text-gray-400"
                >
                    ليس لديك حساب؟
                    <Link
                        :href="route('register')"
                        class="text-[#61CE70] hover:underline"
                        >تسجيل</Link
                    >
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import BaseInput from '@/components/FormElements/BaseInput.vue';

import { Head, useForm } from '@inertiajs/vue3';

// 1. Initialize the form with Inertia's helper
const form = useForm({
    email: 'admin@example.com', // Pre-fill for easy testing
    password: 'password',
    remember: false,
});

// 2. Create a submit function that posts to the Laravel route
const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>
