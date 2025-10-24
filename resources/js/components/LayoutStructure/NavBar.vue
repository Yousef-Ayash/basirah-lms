<script setup>
import ThemeSwitcher from '@/components/Misc/ThemeSwitcher.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import Logo from '../Logo.vue';
import { Inertia } from '@inertiajs/inertia';

const page = usePage();
const user = computed(() => page.props.auth.user);
const isAdmin = computed(() =>
    user.value?.roles?.some((role) => role.name === 'admin'),
);
const viewingAsStudent = computed(() => page.props.auth.viewingAsStudent);

const isOpen = ref(false);
const props = defineProps({
    links: { type: Array, required: true, default: () => [] },
});

const profileRoute = computed(() => route('profile.edit'));
const dashboardUrl = computed(() => route('dashboard'));

function logout() {
    Inertia.post(route('logout'));
}
</script>

<template>
    <nav
        class="sticky top-0 z-50 mb-6 flex items-center justify-between border-b border-gray-200 bg-white px-6 py-3 shadow backdrop-blur-sm transition-colors duration-300 md:grid md:grid-cols-[1fr_70%_1fr] dark:border-gray-800 dark:bg-gray-800/80"
    >
        <div class="flex justify-start">
            <Link
                href="/"
                class="flex flex-row items-center justify-center gap-4"
            >
                <span class="h-16 w-16 rounded-lg bg-white">
                    <Logo />
                </span>
                <span
                    class="flex h-16 w-16 items-center justify-center rounded-lg bg-white p-1"
                >
                    <img src="@i/Logo2.jpg" />
                </span>
            </Link>
        </div>

        <div
            class="hidden items-center justify-center space-x-4 text-sm font-medium md:flex md:w-auto"
        >
            <ul class="flex space-x-4">
                <li v-for="(item, index) in props.links" :key="index">
                    <Link
                        :href="item.to"
                        class="hover:text-[#61CE70]"
                        :class="{
                            'text-[#61CE70]': $page.url.startsWith(item.to),
                        }"
                    >
                        {{ item.label }}
                    </Link>
                </li>
            </ul>
            <ThemeSwitcher />
        </div>

        <div class="flex items-center justify-end space-x-3">
            <template class="block md:hidden">
                <ThemeSwitcher />
            </template>

            <Link
                v-if="isAdmin && !viewingAsStudent"
                :href="route('admin.view-as-student')"
                class="hidden text-sm text-blue-500 hover:underline md:block"
            >
                عرض كطالب
            </Link>

            <Link
                :href="profileRoute"
                class="rounded-full p-2 hover:bg-gray-200 dark:hover:bg-gray-700"
                aria-label="الملف الشخصي"
            >
                <div
                    class="hidden text-sm text-gray-600 md:block dark:text-gray-300"
                >
                    <span class="font-medium">{{ user?.name || 'زائر' }}</span>
                </div>
            </Link>

            <Link
                href="#"
                as="button"
                @click.prevent="logout"
                class="hidden text-sm font-medium hover:cursor-pointer hover:text-[#61CE70] md:block"
            >
                تسجيل الخروج
            </Link>

            <button
                @click="isOpen = !isOpen"
                class="rounded p-2 text-gray-700 md:hidden dark:text-white"
            >
                <svg
                    v-if="!isOpen"
                    class="h-6 w-6"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16"
                    />
                </svg>
                <svg
                    v-else
                    class="h-6 w-6"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"
                    />
                </svg>
            </button>
        </div>

        <div
            v-if="isOpen"
            class="absolute top-full left-0 z-50 w-full bg-white px-6 shadow-md md:hidden dark:bg-gray-800"
        >
            <ul class="text-m flex flex-col space-y-3 p-4 font-medium">
                <li v-for="(item, index) in props.links" :key="'m-' + index">
                    <Link
                        :href="item.to"
                        @click="isOpen = false"
                        class="block hover:text-[#61CE70]"
                        :class="{
                            'text-[#61CE70]': $page.url.startsWith(item.to),
                        }"
                    >
                        {{ item.label }}
                    </Link>
                </li>

                <li v-if="isAdmin && !viewingAsStudent">
                    <Link
                        :href="route('admin.view-as-student')"
                        @click="isOpen = false"
                        class="block text-blue-500 hover:underline"
                    >
                        عرض كطالب
                    </Link>
                </li>

                <li class="border-t pt-3 dark:border-gray-700">
                    <Link :href="profileRoute" @click="isOpen = false">
                        <span class="me-2 font-medium">{{
                            user?.name || 'زائر'
                        }}</span>
                    </Link>
                </li>
                <li>
                    <Link
                        href="#"
                        as="button"
                        @click.prevent="logout"
                        class="w-full text-right hover:cursor-pointer hover:text-[#61CE70]"
                    >
                        تسجيل الخروج
                    </Link>
                </li>
            </ul>
        </div>
    </nav>
</template>
