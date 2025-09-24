<template>
    <Card class="space-y-4">
        <BaseInput :label="__('labels.full_name')" v-model="modelValue.name" :error="modelValue.errors.name" required />
        <BaseInput :label="__('labels.email_address')" type="email" v-model="modelValue.email" :error="modelValue.errors.email" required />
        <BaseInput
            :label="__('common.password')"
            type="password"
            v-model="modelValue.password"
            :error="modelValue.errors.password"
            :placeholder="__('labels.password_leave_blank')"
        />

        <BaseSelect :label="__('common.level')" v-model="modelValue.level_id" :error="modelValue.errors.level_id">
            <option :value="null">{{ __('labels.no_level_assigned') }}</option>
            <option v-for="level in levels" :key="level.id" :value="level.id">{{ level.name }}</option>
        </BaseSelect>

        <div class="flex items-center space-x-2 pt-2">
            <input type="checkbox" id="is_approved" v-model="modelValue.is_approved" class="h-4 w-4 rounded accent-[#61CE70]" />
            <label for="is_approved">{{ __('labels.account_is_approved') }}</label>
        </div>
    </Card>
</template>

<script setup>
import BaseInput from '@/components/FormElements/BaseInput.vue';
import BaseSelect from '@/components/FormElements/BaseSelect.vue';
import Card from '@/components/LayoutStructure/Card.vue';
import { useTranslations } from '@/composables/useTranslations';

const { __ } = useTranslations();

const props = defineProps({
    modelValue: Object, // This will be the Inertia form object
    levels: Array,
});

defineEmits(['update:modelValue']);
</script>
