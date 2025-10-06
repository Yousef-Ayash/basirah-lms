<script setup>
import Card from '@/components/LayoutStructure/Card.vue';

import { Head, Link } from '@inertiajs/vue3';

</script>

<template>
    <div class="flex min-h-screen items-center justify-center bg-gray-50 p-4 dark:bg-gray-900">
        <Head title="في انتظار الموافقة" />
        <div class="w-full max-w-md">
            <Card class="p-6 text-center">
                <h1 class="mb-2 text-2xl font-semibold text-[#61CE70]">تم إرسال التسجيل</h1>
                <p class="text-gray-600 dark:text-gray-400">
                    شكراً لتسجيلك. حسابك حالياً في انتظار موافقة المسؤول.
                </p>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-500">سيتم إعلامك بمجرد تفعيل حسابك.</p>

                <div class="mt-6">
                    <Link :href="route('login')" class="text-sm font-medium text-blue-500 hover:underline">
                        &larr; العودة إلى صفحة تسجيل الدخول
                    </Link>
                </div>
            </Card>
        </div>
    </div>
</template>
