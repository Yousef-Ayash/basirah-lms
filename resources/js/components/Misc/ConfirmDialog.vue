<template>
    <Modal :show="props.show" @close="cancel">
        <template #header>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                {{ props.title }}
            </h3>
        </template>

        <div class="text-sm text-gray-600 dark:text-gray-300">
            {{ props.message }}
        </div>

        <template #footer>
            <div class="mt-4 flex justify-end gap-2">
                <BaseButton class="bg-gray-200 dark:bg-gray-700" @click="cancel">{{ __('common.cancel') }}</BaseButton>
                <BaseButton class="bg-red-500 text-white hover:bg-red-600" @click="confirm">
                    {{ __('common.confirm') }}
                </BaseButton>
            </div>
        </template>
    </Modal>
</template>

<script setup>
import BaseButton from '@/components/FormElements/BaseButton.vue';
import Modal from '@/components/LayoutStructure/Modal.vue';
import { useTranslations } from '@/composables/useTranslations';

const { __ } = useTranslations();

const props = defineProps({
    show: Boolean,
    title: String,
    message: String,
});

const emit = defineEmits(['confirm', 'cancel']);

const cancel = () => emit('cancel');
const confirm = () => emit('confirm');
</script>
