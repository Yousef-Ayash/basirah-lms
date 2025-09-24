<template>
	<div>
		<div class="flex space-x-4 border-b border-gray-300 dark:border-gray-600 mb-4">
			<button
				v-for="(tab, index) in props.tabs"
				:key="index"
				@click="updateTab(index)"
				:class="[
					'px-4 py-2 font-semibold',
					activeTab === index
						? 'border-b-2 border-[#61CE70] text-[#61CE70]'
						: 'text-gray-500 dark:text-gray-400 hover:text-[#61CE70]',
				]"
			>
				{{ tab }}
			</button>
		</div>
		<slot :active="activeTab" />
	</div>
</template>

<script setup>
import { ref, onMounted } from "vue";

const props = defineProps({ tabs: Array });
const emit = defineEmits(["update:activeTab"]);

const activeTab = ref(0);

const updateTab = (index) => {
	activeTab.value = index;
	emit("update:activeTab", index);
	const hash = props.tabs[index].toLowerCase();
	history.replaceState(null, "", `#${hash}`);
};

onMounted(() => {
	const hash = window.location.hash.replace("#", "").toLowerCase();
	const index = props.tabs.findIndex((tab) => tab.toLowerCase() === hash);
	if (index !== -1) {
		activeTab.value = index;
		emit("update:activeTab", index);
	}
});
</script>
