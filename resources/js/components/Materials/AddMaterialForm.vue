<template>
    <div>
        <SectionHeader :title="__('admin.add_new_material')" />
        <form @submit.prevent="submit" enctype="multipart/form-data">
            <Card class="space-y-4">
                <BaseInput
                    :label="__('common.title')"
                    v-model="form.title"
                    :error="form.errors.title"
                    required
                />
                <BaseTextarea
                    :label="__('labels.key_points_optional')"
                    v-model="form.key_points"
                    :error="form.errors.key_points"
                    rows="4"
                    :placeholder="__('placeholders.key_takeaways')"
                />
                <BaseSelect
                    :label="__('labels.material_type')"
                    v-model="form.type"
                    :error="form.errors.type"
                    required
                >
                    <option value="pdf">{{ __('materials.types.pdf') }}</option>
                    <option value="picture">
                        {{ __('materials.types.picture') }}
                    </option>
                    <option value="youtube">
                        {{ __('materials.types.youtube') }}
                    </option>
                </BaseSelect>
                <div v-if="form.type === 'pdf'">
                    <BaseFileInput
                        :label="__('labels.upload_pdf')"
                        @update:modelValue="form.file = $event"
                        :error="form.errors.file"
                        accept=".pdf"
                    />
                </div>
                <div v-if="form.type === 'picture'">
                    <BaseFileInput
                        :label="__('labels.upload_picture')"
                        @update:modelValue="form.file = $event"
                        :error="form.errors.file"
                        accept="image/*"
                    />
                </div>
                <div v-if="form.type === 'youtube'">
                    <BaseInput
                        :label="__('labels.youtube_link')"
                        v-model="form.youtube_link"
                        :error="form.errors.youtube_link"
                        placeholder="https://www.youtube.com/watch?v=..."
                    />
                </div>
                <BaseButton type="submit" :disabled="form.processing">
                    {{ __('buttons.add_material') }}
                </BaseButton>
            </Card>
        </form>
    </div>
</template>

<script setup>
import BaseButton from '@/components/FormElements/BaseButton.vue';
import BaseFileInput from '@/components/FormElements/BaseFileInput.vue';
import BaseInput from '@/components/FormElements/BaseInput.vue';
import BaseSelect from '@/components/FormElements/BaseSelect.vue';
import BaseTextarea from '@/components/FormElements/BaseTextarea.vue';
import Card from '@/components/LayoutStructure/Card.vue';
import SectionHeader from '@/components/LayoutStructure/SectionHeader.vue';
import { useTranslations } from '@/composables/useTranslations';
import { router, useForm } from '@inertiajs/vue3';

const { __ } = useTranslations();

const props = defineProps({
    subjectId: Number,
});

const form = useForm({
    title: '',
    type: 'pdf',
    file: null,
    youtube_link: '',
    key_points: '',
});

const submit = () => {
    form.post(
        route('admin.subjects.materials.store', { subject: props.subjectId }),
        {
            preserveScroll: true,
            forceFormData: true,
            onSuccess: () => {
                form.reset();
                router.reload({ only: ['subject'] });
            },
        },
    );
};
</script>
