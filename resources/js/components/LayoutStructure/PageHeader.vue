<template>
    <div class="mb-6 space-y-4">
        <Head v-if="title && !disableHead" :title="title" />

        <div v-if="backUrl" class="-mb-2">
            <Link
                :href="backUrl"
                class="inline-flex items-center gap-1 text-sm text-blue-500 transition-colors hover:text-blue-600 hover:underline dark:text-blue-400"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="16"
                    height="16"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    class="rtl:rotate-180"
                >
                    <path d="M19 12H5" />
                    <path d="M12 19l-7-7 7-7" />
                </svg>
                <span>{{ backLabel }}</span>
            </Link>
        </div>

        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between"
        >
            <div class="flex-1 space-y-1">
                <slot name="title">
                    <h1
                        class="text-2xl font-bold text-gray-900 dark:text-white"
                    >
                        {{ title }}
                    </h1>
                </slot>
                <p
                    v-if="subtitle || $slots.subtitle"
                    class="text-sm text-gray-500 dark:text-gray-400"
                >
                    <slot name="subtitle">{{ subtitle }}</slot>
                </p>
            </div>

            <div
                v-if="$slots.actions"
                class="flex flex-shrink-0 flex-wrap items-center gap-2"
            >
                <slot name="actions" />
            </div>
        </div>

        <transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="transform scale-95 opacity-0"
            enter-to-class="transform scale-100 opacity-100"
        >
            <div v-if="$slots.filters">
                <Card>
                    <div class="w-full">
                        <slot name="filters" />
                    </div>
                </Card>
            </div>
        </transition>
    </div>
</template>

<script setup>
import { Link, Head } from '@inertiajs/vue3';
import Card from '@/components/LayoutStructure/Card.vue';

defineProps({
    title: String,
    subtitle: String,
    backUrl: String,
    backLabel: {
        type: String,
        default: 'العودة',
    },
    disableHead: {
        type: Boolean,
        default: false,
    },
});
</script>
