<script>
import AdminLayout from '@/Pages/Admin/Layout.vue';
import StudentLayout from '@/Pages/Student/Layout.vue';
import { usePage } from '@inertiajs/vue3';


export default {
    layout: (h, page) => {
        const user = usePage().props.auth.user;
        const isAdmin = user.roles?.some((role) => role.name === 'admin');
        const layout = isAdmin ? AdminLayout : StudentLayout;

        return h(layout, () => page);
    },
};
</script>

<script setup>
import BaseButton from '@/components/FormElements/BaseButton.vue';
import BaseSelect from '@/components/FormElements/BaseSelect.vue';
import BaseInput from '@/components/FormElements/BaseInput.vue';
import Card from '@/components/LayoutStructure/Card.vue';
import SectionHeader from '@/components/LayoutStructure/SectionHeader.vue';
import ConfirmDialog from '@/components/Misc/ConfirmDialog.vue';
import { useForm, Head, } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useTranslations } from '@/composables/useTranslations';

const {__} = useTranslations();

const props = defineProps({
    mustVerifyEmail: Boolean,
    status: String,
    user: Object
});

// --- Form for Profile Information ---
const profileForm = useForm({
    name: props.user.name,
    email: props.user.email,
    locale: props.user.locale || 'ar',
});

const updateProfileInformation = () => {
    profileForm.patch(route('profile.update'), {
        onSuccess: () => {
            window.location.reload();
        },
    });
};

// --- Form for Updating Password ---
const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    passwordForm.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => passwordForm.reset(),
        onError: () => {
            if (passwordForm.errors.password) {
                passwordForm.reset('password', 'password_confirmation');
            }
        },
    });
};

// --- Logic for Deleting Account ---
const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);
const deleteForm = useForm({ password: '' });

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;
};

const deleteUser = () => {
    deleteForm.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => deleteForm.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;
    deleteForm.reset();
};


</script>

<template>
    <div>
        <Head :title="__('profile.profile_settings')" />
        <SectionHeader :title="__('profile.profile_settings')" />

        <div class="mx-auto max-w-3xl space-y-6">
            <Card>
                <header>
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('profile.profile_information') }}</h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('profile.profile_information_description') }}</p>
                </header>
                <form @submit.prevent="updateProfileInformation" class="mt-6 space-y-6">
                    <BaseInput :label="__('common.name')" v-model="profileForm.name" :error="profileForm.errors.name" required />
                    <BaseInput :label="__('common.email')" type="email" v-model="profileForm.email" :error="profileForm.errors.email" required />
                    <BaseSelect :label="__('common.language')" v-model="profileForm.locale">
                        <option value="ar">العربية (Arabic)</option>
                        <option value="en">English (الانكليزية)</option>
                    </BaseSelect>
                    <div class="flex items-center gap-4">
                        <BaseButton :disabled="profileForm.processing">{{ __('buttons.save') }}</BaseButton>
                        <Transition enter-from-class="opacity-0" leave-to-class="opacity-0" class="transition ease-in-out">
                            <p v-if="profileForm.recentlySuccessful" class="text-sm text-gray-600 dark:text-gray-400">{{ __('messages.saved') }}</p>
                        </Transition>
                    </div>
                </form>
            </Card>

            <Card>
                <header>
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('profile.update_password') }}</h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('profile.update_password_description') }}</p>
                </header>
                <form @submit.prevent="updatePassword" class="mt-6 space-y-6">
                    <BaseInput
                        :label="__('profile.current_password')"
                        type="password"
                        v-model="passwordForm.current_password"
                        :error="passwordForm.errors.current_password"
                    />
                    <BaseInput :label="__('profile.new_password')" type="password" v-model="passwordForm.password" :error="passwordForm.errors.password" />
                    <BaseInput
                        :label="__('profile.confirm_password')"
                        type="password"
                        v-model="passwordForm.password_confirmation"
                        :error="passwordForm.errors.password_confirmation"
                    />
                    <div class="flex items-center gap-4">
                        <BaseButton :disabled="passwordForm.processing">{{ __('buttons.save') }}</BaseButton>
                        <Transition enter-from-class="opacity-0" leave-to-class="opacity-0" class="transition ease-in-out">
                            <p v-if="passwordForm.recentlySuccessful" class="text-sm text-gray-600 dark:text-gray-400">{{ __('messages.saved') }}</p>
                        </Transition>
                    </div>
                </form>
            </Card>

            <Card>
                <header>
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('profile.delete_account') }}</h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        {{ __('profile.delete_account_description') }}
                    </p>
                </header>
                <BaseButton @click="confirmUserDeletion" class="mt-4 bg-red-600 hover:bg-red-700">{{ __('profile.delete_account') }}</BaseButton>
            </Card>
        </div>

        <ConfirmDialog
            :show="confirmingUserDeletion"
            @close="closeModal"
            :title="__('profile.delete_account')"
            :message="__('profile.delete_account_confirm')"
        >
            <div class="mt-4">
                <BaseInput
                    ref="passwordInput"
                    v-model="deleteForm.password"
                    type="password"
                    :label="__('common.password')"
                    :placeholder="__('common.password')"
                    :error="deleteForm.errors.password"
                    @keyup.enter="deleteUser"
                />
            </div>
            <template #footer>
                <div class="mt-4 flex justify-end gap-2">
                    <BaseButton @click="closeModal" class="bg-gray-200 dark:bg-gray-700">{{ __('common.cancel') }}</BaseButton>
                    <BaseButton @click="deleteUser" class="bg-red-500 text-white hover:bg-red-600" :disabled="deleteForm.processing">
                        {{ __('common.confirm') }}
                    </BaseButton>
                </div>
            </template>
        </ConfirmDialog>
    </div>
</template>
