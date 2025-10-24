<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref, onMounted, onBeforeUnmount, watch } from 'vue';
import Logo from '../Logo.vue';
import { Inertia } from '@inertiajs/inertia';
import WhatsAppIcon from '../Icons/WhatsAppIcon.vue';
import FacebookIcon from '../Icons/FacebookIcon.vue';
import YoutubeIcon from '../Icons/YoutubeIcon.vue';

const props = defineProps({
    links: { type: Array, required: true, default: () => [] },
});

const page = usePage();
const user = computed(() => page.props.auth.user);
const isLoggedIn = computed(() => !!user.value);

// Mobile menu open state
const isOpen = ref(false);

// Separate dropdown states
const desktopDropdown = ref(null);
const mobileDropdown = ref(null);

// Toggle functions
const toggleDesktopDropdown = (label) => {
    desktopDropdown.value = desktopDropdown.value === label ? null : label;
};

const toggleMobileDropdown = (label) => {
    mobileDropdown.value = mobileDropdown.value === label ? null : label;
};

// Close desktop dropdown on outside click
const handleClickOutside = (e) => {
    if (!e.target.closest('.nav-item')) desktopDropdown.value = null;
};

onMounted(() => document.addEventListener('click', handleClickOutside));
onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside);
});

const profileRoute = computed(() => route('profile.edit'));
const dashboardUrl = computed(() => route('dashboard'));

function logout() {
    Inertia.post(route('logout'));
}

watch(isOpen, (value) => {
    if (!value) mobileDropdown.value = null;
});

watch(
    () => page.url,
    () => {
        // Close all menus and dropdowns on route change
        isOpen.value = false;
        desktopDropdown.value = null;
        mobileDropdown.value = null;
    },
);
</script>

<template>
    <nav
        class="sticky top-0 z-50 mb-6 flex items-center justify-between border-b border-gray-200 px-8 py-3 shadow backdrop-blur-sm transition-colors duration-300 md:grid md:grid-cols-3"
    >
        <div class="flex items-center justify-start gap-10">
            <!-- Logo -->
            <div>
                <Link
                    :href="route('home')"
                    class="flex flex-row items-center justify-center gap-4"
                >
                    <span class="h-16 w-16 rounded-lg bg-white p-1">
                        <Logo />
                    </span>
                    <span
                        class="flex h-16 w-16 items-center justify-center rounded-lg bg-white p-1"
                    >
                        <img src="@i/Logo2.jpg" />
                    </span>
                </Link>
            </div>
            <!-- Icons -->
            <div class="flex flex-row justify-between gap-2">
                <span
                    class="h-10 w-10 rounded-full p-1 transition-transform hover:scale-110 hover:bg-green-200"
                >
                    <a
                        href="https://www.youtube.com/@basirahonline"
                        target="_blank"
                        aria-label="YouTube"
                    >
                        <YoutubeIcon />
                    </a>
                </span>
                <span
                    class="h-10 w-10 rounded-full p-[7px] transition-transform hover:scale-110 hover:bg-green-200"
                >
                    <a
                        href="https://wa.me/+963930101566"
                        target="_blank"
                        aria-label="WhatsApp"
                    >
                        <WhatsAppIcon />
                    </a>
                </span>
                <span
                    class="h-10 w-10 rounded-full p-1 transition-transform hover:scale-110 hover:bg-green-200"
                >
                    <a
                        href="https://www.facebook.com/basirahonline"
                        target="_blank"
                        aria-label="Facebook"
                    >
                        <FacebookIcon />
                    </a>
                </span>
            </div>
        </div>

        <!-- Links -->
        <div
            class="hidden items-center justify-center space-x-4 text-sm font-medium md:flex md:w-auto"
        >
            <ul
                class="m-0 flex list-none flex-col gap-4 p-0 md:flex-row md:gap-6"
            >
                <li
                    v-for="link in links"
                    :key="link.label"
                    class="nav-item relative"
                >
                    <!-- Regular Link -->
                    <Link
                        v-if="!link.dropdown"
                        :href="link.to"
                        class="inline-flex items-center justify-center rounded-full bg-green-600 px-4 py-2 text-sm text-white shadow-md transition hover:scale-105 hover:bg-green-700 active:scale-95"
                    >
                        {{ link.label }}
                    </Link>

                    <!-- Dropdown -->
                    <div v-else class="inline-block">
                        <button
                            class="inline-flex items-center justify-center gap-1 rounded-full bg-green-600 px-4 py-2 text-sm text-white shadow-md transition hover:scale-105 hover:cursor-pointer hover:bg-green-700 active:scale-95"
                            @click.stop="toggleDesktopDropdown(link.label)"
                            aria-haspopup="true"
                            :aria-expanded="desktopDropdown === link.label"
                        >
                            {{ link.label }}
                            <svg
                                class="h-4 w-4 transition-transform"
                                :class="{
                                    'rotate-180':
                                        desktopDropdown === link.label,
                                }"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 9l-7 7-7-7"
                                />
                            </svg>
                        </button>

                        <ul
                            v-if="desktopDropdown === link.label"
                            class="absolute left-0 mt-2 w-48 list-none rounded-lg border border-green-100 bg-white p-1 shadow-lg"
                        >
                            <li v-for="(item, i) in link.dropdown" :key="i">
                                <Link
                                    :href="item.to"
                                    class="flex w-full rounded-lg px-4 py-2 text-right whitespace-nowrap text-green-700 transition hover:bg-green-50 hover:text-green-900"
                                    @click="desktopDropdown = null"
                                >
                                    {{ item.label }}
                                </Link>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
        </div>

        <!-- Action (?) -->
        <div class="flex items-center justify-end space-x-3">
            <div class="hidden flex-col gap-2 md:flex md:flex-row">
                <Link
                    :href="!isLoggedIn ? route('login') : dashboardUrl"
                    class="inline-flex items-center justify-center gap-1 rounded-full bg-green-600 px-4 py-2 text-sm text-white shadow-md transition hover:scale-105 hover:cursor-pointer hover:bg-green-700 active:scale-95"
                >
                    {{ !isLoggedIn ? 'تسجيل الدخول' : 'لوحة التحكم' }}
                </Link>
                <Link
                    :href="route('join')"
                    class="inline-flex items-center justify-center gap-1 rounded-full bg-green-600 px-4 py-2 text-sm text-white shadow-md transition hover:scale-105 hover:cursor-pointer hover:bg-green-700 active:scale-95"
                >
                    انضم إلينا
                </Link>
            </div>

            <button
                @click="isOpen = !isOpen"
                class="rounded p-2 text-gray-700 md:hidden"
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

        <!-- Mobile nav -->
        <div
            v-if="isOpen"
            class="absolute top-full left-0 z-50 w-full bg-white px-6 pb-4 shadow-md md:hidden"
        >
            <!-- Mobile Nav Links -->
            <ul class="flex flex-col divide-y divide-gray-100">
                <li v-for="link in links" :key="link.label" class="py-3">
                    <!-- Normal Link -->
                    <Link
                        v-if="!link.dropdown"
                        :href="link.to"
                        class="block text-right font-medium text-gray-800 hover:text-green-700"
                        @click="isOpen = false"
                    >
                        {{ link.label }}
                    </Link>

                    <!-- Dropdown -->
                    <div v-else>
                        <button
                            class="flex w-full items-center justify-between text-right font-medium text-gray-800"
                            @click="toggleMobileDropdown(link.label)"
                        >
                            <span>{{ link.label }}</span>
                            <svg
                                class="h-4 w-4 transition-transform"
                                :class="{
                                    'rotate-180': mobileDropdown === link.label,
                                }"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 9l-7 7-7-7"
                                />
                            </svg>
                        </button>

                        <transition name="fade">
                            <ul
                                v-if="mobileDropdown === link.label"
                                class="mt-2 flex flex-col gap-1 rounded-lg bg-gray-100 p-2"
                            >
                                <li v-for="(item, i) in link.dropdown" :key="i">
                                    <Link
                                        :href="item.to"
                                        class="block py-1 text-right text-gray-700 transition hover:text-green-700"
                                        @click="isOpen = false"
                                    >
                                        {{ item.label }}
                                    </Link>
                                </li>
                            </ul>
                        </transition>
                    </div>
                </li>
            </ul>

            <!-- Mobile Actions -->
            <div class="mt-4 flex flex-col gap-2">
                <Link
                    :href="!isLoggedIn ? route('login') : dashboardUrl"
                    class="inline-flex items-center justify-center gap-1 rounded-full bg-green-600 px-4 py-2 text-sm text-white shadow-md transition hover:scale-105 hover:cursor-pointer hover:bg-green-700 active:scale-95"
                    @click="isOpen = false"
                >
                    {{ !isLoggedIn ? 'تسجيل الدخول' : 'لوحة التحكم' }}
                </Link>
                <Link
                    :href="route('join')"
                    class="inline-flex items-center justify-center gap-1 rounded-full bg-green-600 px-4 py-2 text-sm text-white shadow-md transition hover:scale-105 hover:cursor-pointer hover:bg-green-700 active:scale-95"
                    @click="isOpen = false"
                >
                    انضم إلينا
                </Link>
            </div>
        </div>
    </nav>
</template>
