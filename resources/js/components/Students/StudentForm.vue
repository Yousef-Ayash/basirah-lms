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
        <!-- <BaseInput
            label="عنوان البريد الإلكتروني"
            type="email"
            v-model="modelValue.email"
            :error="modelValue.errors.email"
            required
        /> -->
        <BaseInput
            label="كلمة المرور"
            type="password"
            v-model="modelValue.password"
            :error="modelValue.errors.password"
            placeholder="اتركه فارغاً لعدم التغيير"
        />

        <BaseSelect
            label="المستوى"
            v-model="modelValue.level_id"
            :error="modelValue.errors.level_id"
        >
            <option :value="null">لم يتم تعيين مستوى</option>
            <option v-for="level in levels" :key="level.id" :value="level.id">
                {{ level.name }}
            </option>
        </BaseSelect>

        <div class="flex items-center space-x-2 pt-2">
            <input
                type="checkbox"
                id="is_approved"
                v-model="modelValue.is_approved"
                class="h-4 w-4 rounded accent-[#61CE70]"
            />
            <label for="is_approved">الحساب مفعل</label>
        </div>
    </Card>
</template>

<script setup>
import BaseInput from '@/components/FormElements/BaseInput.vue';
import BaseSelect from '@/components/FormElements/BaseSelect.vue';
import Card from '@/components/LayoutStructure/Card.vue';

const props = defineProps({
    modelValue: Object, // This will be the Inertia form object
    levels: Array,
});

defineEmits(['update:modelValue']);
</script>
