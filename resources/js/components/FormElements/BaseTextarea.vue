<template>
	<div class="space-y-1">
		<label
			v-if="label"
			:for="id"
			class="block text-sm font-medium text-gray-700 dark:text-gray-300"
		>
			{{ label }}
		</label>
		<textarea
			:id="id"
			v-bind="$attrs"
			:value="modelValue"
			@input="$emit('update:modelValue', $event.target.value)"
			class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#61CE70]"
		></textarea>
		<p v-if="error" class="text-sm text-red-500">{{ error }}</p>
	</div>
</template>

<script setup>
defineProps({
	modelValue: String,
	label: String,
	error: String,
	id: String,
});
</script>
