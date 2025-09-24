<script setup>
import BaseButton from '@/components/FormElements/BaseButton.vue';
import BaseInput from '@/components/FormElements/BaseInput.vue';
import Card from '@/components/LayoutStructure/Card.vue';
import Alert from '@/components/Misc/Alert.vue';
import { useTranslations } from '@/composables/useTranslations';
import { Head, Link, useForm } from '@inertiajs/vue3';

const { __ } = useTranslations();

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
        <Head :title="__('auth.forgot_password')" />
        <div class="w-full max-w-md">
            <Card class="p-6">
                <h1 class="mb-2 text-2xl font-semibold text-[#61CE70]">{{ __('auth.forgot_password') }}</h1>
                <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('auth.forgot_password_message') }}
                </p>

                <Alert v-if="status" :message="status" type="success" class="mb-4" />

                <form @submit.prevent="submit" class="space-y-4">
                    <BaseInput :label="__('common.email')" type="email" v-model="form.email" :error="form.errors.email" required autofocus />

                    <div class="flex items-center justify-between">
                        <Link :href="route('login')" class="text-sm text-gray-600 hover:underline dark:text-gray-300">
                            &larr; {{ __('auth.back_to_login') }}
                        </Link>
                        <BaseButton :disabled="form.processing"> {{ __('auth.email_password_reset_link') }} </BaseButton>
                    </div>
                </form>
            </Card>
        </div>
    </div>
</template>
