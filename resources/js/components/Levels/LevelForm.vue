<template>
    <Card class="space-y-4">
        <BaseInput
            label="اسم المستوى"
            v-model="modelValue.name"
            :error="modelValue.errors.name"
            placeholder="المستوى..."
            required
        />
        <div>
            <BaseInput
                label="الترتيب"
                type="number"
                v-model="modelValue.order"
                :error="modelValue.errors.order"
                placeholder="اتركه فارغاً لعدم التغيير"
                required
            />
            <p class="mt-1 text-xs text-gray-500">
                تركه فارغاً أو 0 لعدم تغيير الترتيب.
            </p>
        </div>
    </Card>
</template>

<script setup>
import BaseInput from '../FormElements/BaseInput.vue';
import Card from '../LayoutStructure/Card.vue';

defineProps({
    modelValue: Object, // Inertia form object
    levels: Array,
});

defineEmits(['update:modelValue']);
</script>
