<template>
	<div :class="['animate-pulse rounded', props.width, props.height, props.classes]"></div>
</template>

<script setup>
const props = defineProps({
	width: { type: String, default: "w-full" },
	height: { type: String, default: "h-4" },
	classes: { type: String, default: "" },
});
</script>
