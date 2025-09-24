<template>
	<div
		v-if="message"
		:class="[
			'p-3 rounded-md text-sm mb-4',
			type === 'success'
				? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
				: type === 'error'
					? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
					: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
		]"
	>
		{{ message }}
	</div>
</template>

<script setup>
defineProps({
	type: {
		type: String,
		default: "info", // success, error, info
	},
	message: String,
});
</script>
