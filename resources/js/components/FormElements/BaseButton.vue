<template>
    <component
        :is="as"
        class="inline-flex items-center justify-center rounded-lg bg-[#61CE70] px-4 py-2 font-semibold text-white transition hover:cursor-pointer hover:bg-[#80ff93] disabled:opacity-50"
        :disabled="disabled"
    >
        <slot />
    </component>
</template>

<script setup>
defineProps({
    as: { type: String, default: 'button' },
    disabled: Boolean,
});
</script>
