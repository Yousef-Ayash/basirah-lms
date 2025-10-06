<template>
    <Card class="space-y-4">
        <BaseInput label="العنوان" v-model="props.modelValue.title" :error="props.modelValue.errors.title" required />
        <BaseTextarea
            label="الوصف"
            v-model="props.modelValue.description"
            :error="props.modelValue.errors.description"
            rows="5"
        />
        <BaseSelect label="المستوى" v-model="props.modelValue.level_id" :error="props.modelValue.errors.level_id">
            <option :value="0" disabled>-- بلا مستوى --</option>
            <option v-for="level in levels" :key="level.id" :value="level.id">
                {{ level.name }}
            </option>
        </BaseSelect>
        <BaseFileInput
            label="صورة الغلاف (اختياري)"
            @update:modelValue="props.modelValue.cover = $event"
            :error="props.modelValue.errors.cover"
            accept="image/*"
        />
    </Card>
</template>

<script setup>
import BaseFileInput from '@/components/FormElements/BaseFileInput.vue';
import BaseInput from '@/components/FormElements/BaseInput.vue';
import BaseSelect from '@/components/FormElements/BaseSelect.vue';
import BaseTextarea from '@/components/FormElements/BaseTextarea.vue';
import Card from '@/components/LayoutStructure/Card.vue';

const props = defineProps({
    modelValue: Object, // The Inertia form object
    levels: Array,
});

defineEmits(['update:modelValue']);
</script>
