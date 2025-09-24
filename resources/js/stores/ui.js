import { defineStore } from "pinia";
import { ref } from "vue";

export const useUiStore = defineStore("ui", () => {
	const isLoading = ref(false);
	const notifications = ref([]);

	function setLoading(status) {
		isLoading.value = status;
	}

	function showNotification({ message, type = "info" }) {
		notifications.value.push({ id: Date.now(), message, type });
	}

	function removeNotification(id) {
		notifications.value = notifications.value.filter((n) => n.id !== id);
	}

	return {
		isLoading,
		notifications,
		setLoading,
		showNotification,
		removeNotification,
	};
});
