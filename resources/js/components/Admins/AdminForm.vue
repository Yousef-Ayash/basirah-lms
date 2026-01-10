<template>
    <Card class="space-y-4">
        <BaseInput
            label="الاسم الكامل"
            v-model="modelValue.name"
            :error="modelValue.errors.name"
            required
        />
        <BaseInput
            label="رقم الموبايل"
            v-model="modelValue.phone"
            :error="modelValue.errors.phone"
            required
        />
        <BaseInput
            label="كلمة المرور"
            type="password"
            v-model="modelValue.password"
            :error="modelValue.errors.password"
            :placeholder="isEdit ? 'اتركه فارغاً لعدم التغيير' : ''"
            :required="!isEdit"
        />
    </Card>
</template>

<script setup>
import BaseInput from '@/components/FormElements/BaseInput.vue';
import Card from '@/components/LayoutStructure/Card.vue';

const props = defineProps({
    modelValue: Object,
    isEdit: { type: Boolean, default: false },
});

defineEmits(['update:modelValue']);
</script>
