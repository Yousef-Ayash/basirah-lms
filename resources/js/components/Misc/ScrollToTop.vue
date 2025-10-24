<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';

const showButton = ref(false);

const toggleVisibility = () => {
    showButton.value = window.scrollY > 300;
};

const scrollToTop = () => {
    window.scrollTo({
        top: 0,
        behavior: 'smooth',
    });
};

onMounted(() => {
    window.addEventListener('scroll', toggleVisibility);
});

onBeforeUnmount(() => {
    window.removeEventListener('scroll', toggleVisibility);
});
</script>

<template>
    <transition name="fade">
        <button
            v-if="showButton"
            @click="scrollToTop"
            class="fixed right-6 bottom-6 z-50 flex h-12 w-12 items-center justify-center rounded-full bg-green-600 text-white shadow-lg transition-all duration-300 hover:scale-110 hover:bg-green-700 focus:outline-none"
            aria-label="Scroll to top"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="2"
                stroke="currentColor"
                class="h-6 w-6"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M5 15l7-7 7 7"
                />
            </svg>
        </button>
    </transition>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition:
        opacity 0.3s ease,
        transform 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
    transform: translateY(20px);
}
</style>
