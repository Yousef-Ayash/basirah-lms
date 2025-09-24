<template>
	<div class="space-y-1">
		<label v-if="label" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
			{{ label }}
		</label>

		<div
			class="relative border border-dashed rounded-md px-4 py-6 flex justify-center items-center text-sm text-gray-500 dark:text-gray-400 hover:border-[#61CE70] transition cursor-pointer bg-white dark:bg-gray-800"
		>
			<input
				type="file"
				class="absolute inset-0 opacity-0 cursor-pointer"
				:accept="accept"
				@change="handleFileChange"
			/>
			<span>
				{{ selectedFileName || "Click or drag to upload" }}
			</span>
		</div>

		<p v-if="hint" class="text-xs text-gray-400 dark:text-gray-500">
			{{ hint }}
		</p>
	</div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
	modelValue: File,
	label: String,
	hint: String,
	accept: String, // NEW
});

const emit = defineEmits(["update:modelValue"]);

const selectedFileName = computed(() => props.modelValue?.name ?? "");

const handleFileChange = (e) => {
	const file = e.target.files?.[0];
	if (file) {
		emit("update:modelValue", file);
	}
};
</script>
