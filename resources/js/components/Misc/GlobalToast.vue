<template>
	<transition name="slide-fade">
		<div v-if="message" class="fixed right-4 top-4 z-50">
			<div class="px-4 py-2 rounded shadow text-white bg-[#61CE70]">
				{{ message }}
			</div>
		</div>
	</transition>
</template>

<script setup>
import { computed } from "vue";
import { useUiStore } from "@/stores/ui";
const ui = useUiStore();
const message = computed(() => ui.alertMessage);
</script>

<style>
.slide-fade-enter-active {
	transition: all 0.25s ease;
	transform-origin: top right;
}
.slide-fade-leave-active {
	transition: all 0.25s ease;
}
</style>
