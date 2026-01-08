<script setup>
import BaseInput from '@/components/FormElements/BaseInput.vue';
import BaseTextarea from '@/components/FormElements/BaseTextarea.vue';
import BaseFileInput from '@/components/FormElements/BaseFileInput.vue';
import Card from '@/components/LayoutStructure/Card.vue';

defineProps({
    modelValue: Object,
});

function markRemove() {
    local.remove_cover = true;
    emitModel();
}

function undoRemove() {
    local.remove_cover = false;
    emitModel();
}
</script>

<template>
    <Card class="space-y-4">
        <BaseInput
            label="الاسم"
            v-model="modelValue.name"
            :error="modelValue.errors.name"
            required
        />
        <BaseInput
            label="الترتيب"
            type="number"
            min="0"
            v-model="modelValue.order"
            :error="modelValue.errors.order"
            required
        />
        <BaseTextarea
            label="السيرة"
            v-model="modelValue.bio"
            :error="modelValue.errors.bio"
            rows="5"
        />
        <BaseFileInput
            label="صورة"
            @update:modelValue="modelValue.photo = $event"
            :error="modelValue.errors.photo"
            accept="image/*"
        />

        <div
            v-if="
                modelValue.photo_url &&
                !modelValue.photo &&
                !modelValue.remove_photo
            "
            class="group relative mt-2 inline-block"
        >
            <img
                :src="modelValue.photo_url"
                class="h-32 w-32 rounded-full border border-gray-200 object-cover"
                alt="teacher photo"
            />
            <button
                type="button"
                @click="modelValue.remove_photo = true"
                class="absolute top-0 right-0 rounded-full bg-red-500 p-1 text-white shadow hover:bg-red-600"
                title="حذف الصورة"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"
                    />
                </svg>
            </button>
        </div>

        <div v-if="modelValue.remove_photo" class="mt-2 text-sm text-red-500">
            سيتم حذف الصورة الحالية عند الحفظ.
            <button
                type="button"
                @click="modelValue.remove_photo = false"
                class="mx-2 text-gray-500 underline hover:text-gray-700"
            >
                تراجع
            </button>
        </div>
    </Card>
</template>
