<script setup>
import BaseButton from '@/components/FormElements/BaseButton.vue';
import BaseInput from '@/components/FormElements/BaseInput.vue';
import Card from '@/components/LayoutStructure/Card.vue';
import Alert from '@/components/Misc/Alert.vue';

import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <div class="flex min-h-screen items-center justify-center bg-gray-50 p-4 dark:bg-gray-900">
        <Head title="هل نسيت كلمة المرور؟" />
        <div class="w-full max-w-md">
            <Card class="p-6">
                <h1 class="mb-2 text-2xl font-semibold text-[#61CE70]">هل نسيت كلمة المرور؟</h1>
                <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                    لا مشكلة. فقط أدخل عنوان بريدك الإلكتروني وسنرسل لك رابط إعادة تعيين كلمة المرور.
                </p>

                <Alert v-if="status" :message="status" type="success" class="mb-4" />

                <form @submit.prevent="submit" class="space-y-4">
                    <BaseInput label="البريد الإلكتروني" type="email" v-model="form.email" :error="form.errors.email" required autofocus />

                    <div class="flex items-center justify-between">
                        <Link :href="route('login')" class="text-sm text-gray-600 hover:underline dark:text-gray-300">
                            &larr; العودة إلى صفحة تسجيل الدخول
                        </Link>
                        <BaseButton :disabled="form.processing"> إرسال رابط إعادة تعيين كلمة المرور </BaseButton>
                    </div>
                </form>
            </Card>
        </div>
    </div>
</template>
