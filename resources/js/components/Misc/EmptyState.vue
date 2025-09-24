<template>
	<div class="text-center py-12">
		<div v-if="$slots.icon" class="mb-4">
			<slot name="icon" />
		</div>
		<p class="text-lg font-semibold text-gray-700 dark:text-gray-300">{{ props.message }}</p>
		<p v-if="sub" class="text-sm text-gray-500 dark:text-gray-400 mt-2">{{ props.sub }}</p>
		<div v-if="$slots.action" class="mt-4">
			<slot name="action" />
		</div>
	</div>
</template>

<script setup>
const props = defineProps({
	message: { type: String, required: true },
	sub: { type: String, default: "" },
});
</script>
