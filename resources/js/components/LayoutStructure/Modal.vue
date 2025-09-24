<template>
	<div
		v-if="show"
		class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
	>
		<div
			class="bg-white dark:bg-gray-900 rounded-xl shadow-xl max-w-md w-full p-6 mx-2 relative"
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
				class="absolute top-2 right-2 text-gray-400 hover:text-gray-600 dark:hover:text-white"
				@click="$emit('close')"
			>
				✕
			</button>
		</div>
	</div>
</template>

<script setup>
defineProps({ show: Boolean });
defineEmits(["close"]);
</script>
