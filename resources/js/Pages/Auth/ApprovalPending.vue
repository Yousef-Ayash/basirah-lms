<script setup>
import Card from '@/components/LayoutStructure/Card.vue';
import { useTranslations } from '@/composables/useTranslations';
import { Head, Link } from '@inertiajs/vue3';

const { __ } = useTranslations();
</script>

<template>
    <div class="flex min-h-screen items-center justify-center bg-gray-50 p-4 dark:bg-gray-900">
        <Head :title="__('auth.approval_pending')" />
        <div class="w-full max-w-md">
            <Card class="p-6 text-center">
                <h1 class="mb-2 text-2xl font-semibold text-[#61CE70]">{{ __('auth.registration_submitted') }}</h1>
                <p class="text-gray-600 dark:text-gray-400">
                    {{ __('auth.approval_message') }}
                </p>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-500">{{ __('auth.approval_submessage') }}</p>

                <div class="mt-6">
                    <Link :href="route('login')" class="text-sm font-medium text-blue-500 hover:underline">
                        &larr; {{ __('auth.back_to_login') }}
                    </Link>
                </div>
            </Card>
        </div>
    </div>
</template>
