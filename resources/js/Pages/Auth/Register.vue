<template>
    <div class="flex min-h-screen items-center justify-center bg-gray-50 p-4 dark:bg-gray-900">
        <Head :title="__('common.register')" />
        <div class="w-full max-w-md">
            <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                <h1 class="mb-4 text-2xl font-semibold text-[#61CE70]">{{ __('auth.create_account') }}</h1>

                <form @submit.prevent="submit" class="space-y-4">
                    <BaseInput :label="__('labels.full_name')" v-model="form.name" :error="form.errors.name" required autocomplete="name" />
                    <BaseInput
                        :label="__('common.email')"
                        type="email"
                        v-model="form.email"
                        :error="form.errors.email"
                        required
                        autocomplete="username"
                    />
                    <BaseInput
                        :label="__('common.password')"
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

                    <div class="flex items-center justify-between">
                        <button
                            class="rounded bg-[#61CE70] px-4 py-2 font-medium text-white disabled:opacity-60"
                            type="submit"
                            :disabled="form.processing"
                        >
                            <span v-if="form.processing">{{ __('auth.creating_account') }}</span>
                            <span v-else>{{ __('auth.create_account') }}</span>
                        </button>

                        <Link :href="route('login')" class="text-sm text-gray-600 hover:underline dark:text-gray-300">
                            {{ __('auth.already_registered') }}
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import BaseInput from '@/components/FormElements/BaseInput.vue';
import { useTranslations } from '@/composables/useTranslations';
import { Head, useForm } from '@inertiajs/vue3';

const { __ } = useTranslations();

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
