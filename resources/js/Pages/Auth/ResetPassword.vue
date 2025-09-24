<script setup>
import BaseButton from '@/components/FormElements/BaseButton.vue';
import BaseInput from '@/components/FormElements/BaseInput.vue';
import Card from '@/components/LayoutStructure/Card.vue';
import { useTranslations } from '@/composables/useTranslations';
import { Head, useForm } from '@inertiajs/vue3';

const { __ } = useTranslations();

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <div class="flex min-h-screen items-center justify-center bg-gray-50 p-4 dark:bg-gray-900">
        <Head :title="__('auth.reset_password')" />
        <div class="w-full max-w-md">
            <Card class="p-6">
                <h1 class="mb-4 text-2xl font-semibold text-[#61CE70]">{{ __('auth.reset_password') }}</h1>

                <form @submit.prevent="submit" class="space-y-4">
                    <BaseInput
                        :label="__('common.email')"
                        type="email"
                        v-model="form.email"
                        :error="form.errors.email"
                        required
                        disabled
                        class="bg-gray-100 dark:bg-gray-700"
                    />

                    <BaseInput
                        :label="__('profile.new_password')"
                        type="password"
                        v-model="form.password"
                        :error="form.errors.password"
                        required
                        autocomplete="new-password"
                    />

                    <BaseInput
                        :label="__('profile.confirm_password')"
                        type="password"
                        v-model="form.password_confirmation"
                        :error="form.errors.password_confirmation"
                        required
                        autocomplete="new-password"
                    />

                    <div class="flex items-center justify-end">
                        <BaseButton type="submit" :disabled="form.processing"> {{ __('auth.reset_password') }} </BaseButton>
                    </div>
                </form>
            </Card>
        </div>
    </div>
</template>
