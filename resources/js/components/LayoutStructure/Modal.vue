<template>
    <div
        v-if="show"
        class="bg-opacity-50 fixed inset-0 z-50 flex items-center justify-center bg-black"
    >
        <div
            class="relative mx-2 w-full max-w-md rounded-xl bg-white p-6 shadow-xl dark:bg-gray-900"
        >
            <!-- Header Slot -->
            <div v-if="$slots.header" class="mb-4">
                <slot name="header" />
            </div>

            <!-- Default Slot -->
            <div>
                <slot />
            </div>

            <!-- Footer Slot -->
            <div v-if="$slots.footer" class="mt-6">
                <slot name="footer" />
            </div>

            <!-- Close Button -->
            <button
                class="absolute top-4 left-6 flex h-6 w-6 items-center justify-center rounded-sm bg-gray-700 hover:cursor-pointer hover:bg-gray-600"
                @click="$emit('close')"
            >
                <span class="h-full w-full">
                    <x-icon />
                </span>
            </button>
        </div>
    </div>
</template>

<script setup>
import XIcon from '../Icons/XIcon.vue';

defineProps({ show: Boolean });
defineEmits(['close']);
</script>
