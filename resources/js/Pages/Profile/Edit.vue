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
import { useForm, Head } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    mustVerifyEmail: Boolean,
    status: String,
    user: Object,
});

// --- Form for Profile Information ---
const profileForm = useForm({
    name: props.user.name,
    email: props.user.email,
    // locale: props.user.locale || 'ar',
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
        <Head title="إعدادات الملف الشخصي" />
        <SectionHeader title="إعدادات الملف الشخصي" />

        <div class="mx-auto max-w-3xl space-y-6">
            <Card>
                <header>
                    <h2
                        class="text-lg font-medium text-gray-900 dark:text-gray-100"
                    >
                        معلومات الملف الشخصي
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        تحديث معلومات الملف الشخصي وعنوان البريد الإلكتروني لحسابك.
                    </p>
                </header>
                <form
                    @submit.prevent="updateProfileInformation"
                    class="mt-6 space-y-6"
                >
                    <BaseInput
                        label="الاسم"
                        v-model="profileForm.name"
                        :error="profileForm.errors.name"
                        required
                    />
                    <BaseInput
                        label="البريد الإلكتروني"
                        type="email"
                        v-model="profileForm.email"
                        :error="profileForm.errors.email"
                        required
                    />
                    <!-- <BaseSelect
                        label="اللغة"
                        v-model="profileForm.locale"
                    >
                        <option value="ar">العربية (Arabic)</option>
                        <option value="en">English (الانكليزية)</option>
                    </BaseSelect> -->
                    <div class="flex items-center gap-4">
                        <BaseButton :disabled="profileForm.processing">حفظ</BaseButton>
                        <Transition
                            enter-from-class="opacity-0"
                            leave-to-class="opacity-0"
                            class="transition ease-in-out"
                        >
                            <p
                                v-if="profileForm.recentlySuccessful"
                                class="text-sm text-gray-600 dark:text-gray-400"
                            >
                                تم الحفظ.
                            </p>
                        </Transition>
                    </div>
                </form>
            </Card>

            <Card>
                <header>
                    <h2
                        class="text-lg font-medium text-gray-900 dark:text-gray-100"
                    >
                        تحديث كلمة المرور
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        تأكد من أن حسابك يستخدم كلمة مرور طويلة وعشوائية للحفاظ على الأمان.
                    </p>
                </header>
                <form @submit.prevent="updatePassword" class="mt-6 space-y-6">
                    <BaseInput
                        label="كلمة المرور الحالية"
                        type="password"
                        v-model="passwordForm.current_password"
                        :error="passwordForm.errors.current_password"
                    />
                    <BaseInput
                        label="كلمة المرور الجديدة"
                        type="password"
                        v-model="passwordForm.password"
                        :error="passwordForm.errors.password"
                    />
                    <BaseInput
                        label="تأكيد كلمة المرور"
                        type="password"
                        v-model="passwordForm.password_confirmation"
                        :error="passwordForm.errors.password_confirmation"
                    />
                    <div class="flex items-center gap-4">
                        <BaseButton :disabled="passwordForm.processing">حفظ</BaseButton>
                        <Transition
                            enter-from-class="opacity-0"
                            leave-to-class="opacity-0"
                            class="transition ease-in-out"
                        >
                            <p
                                v-if="passwordForm.recentlySuccessful"
                                class="text-sm text-gray-600 dark:text-gray-400"
                            >
                                تم الحفظ.
                            </p>
                        </Transition>
                    </div>
                </form>
            </Card>

            <Card>
                <header>
                    <h2
                        class="text-lg font-medium text-gray-900 dark:text-gray-100"
                    >
                        حذف الحساب
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        بمجرد حذف حسابك، سيتم حذف جميع موارده وبياناته بشكل دائم.
                    </p>
                </header>
                <BaseButton
                    @click="confirmUserDeletion"
                    class="mt-4 bg-red-600 hover:bg-red-700"
                    >حذف الحساب</BaseButton
                >
            </Card>
        </div>

        <ConfirmDialog
            :show="confirmingUserDeletion"
            @close="closeModal"
            title="حذف الحساب"
            message="هل أنت متأكد من أنك تريد حذف حسابك؟ يرجى إدخال كلمة المرور للتأكيد."
        >
            <div class="mt-4">
                <BaseInput
                    ref="passwordInput"
                    v-model="deleteForm.password"
                    type="password"
                    label="كلمة المرور"
                    placeholder="كلمة المرور"
                    :error="deleteForm.errors.password"
                    @keyup.enter="deleteUser"
                />
            </div>
            <template #footer>
                <div class="mt-4 flex justify-end gap-2">
                    <BaseButton
                        @click="closeModal"
                        class="bg-gray-200 dark:bg-gray-700"
                        >إلغاء</BaseButton
                    >
                    <BaseButton
                        @click="deleteUser"
                        class="bg-red-500 text-white hover:bg-red-600"
                        :disabled="deleteForm.processing"
                    >
                        تأكيد
                    </BaseButton>
                </div>
            </template>
        </ConfirmDialog>
    </div>
</template>
