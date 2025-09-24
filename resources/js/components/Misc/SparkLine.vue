<template>
	<svg :viewBox="'0 0 ' + w + ' ' + h" :width="w" :height="h" class="block">
		<polyline
			:points="points"
			fill="none"
			stroke="#61CE70"
			stroke-width="2"
			stroke-linejoin="round"
			stroke-linecap="round"
		/>
	</svg>
</template>

<script setup>
import { computed } from "vue";
const props = defineProps({
	data: { type: Array, required: true },
	w: { type: Number, default: 100 },
	h: { type: Number, default: 24 },
});

const points = computed(() => {
	const d = props.data.length ? props.data : [0];
	const max = Math.max(...d);
	const min = Math.min(...d);
	const range = max - min || 1;
	return d
		.map((v, i) => {
			const x = (i / (d.length - 1 || 1)) * props.w;
			const y = props.h - ((v - min) / range) * props.h;
			return `${x},${y}`;
		})
		.join(" ");
});
</script>
