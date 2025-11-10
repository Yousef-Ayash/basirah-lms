<template>
    <div>
        <SectionHeader title="إضافة مقررات جديدة" />
        <form @submit.prevent="submit" enctype="multipart/form-data">
            <Card class="space-y-4">
                <BaseInput
                    label="العنوان"
                    v-model="form.title"
                    :error="form.errors.title"
                    required
                />
                <BaseTextarea
                    label="النقاط الرئيسية (اختياري)"
                    v-model="form.key_points"
                    :error="form.errors.key_points"
                    rows="4"
                    placeholder="أدخل النقاط الرئيسية أو ملخصًا للطلاب..."
                />
                <BaseSelect
                    label="نوع المادة"
                    v-model="form.type"
                    :error="form.errors.type"
                    required
                >
                    <option value="pdf">ملف PDF</option>
                    <option value="picture">صورة</option>
                    <option value="youtube">فيديو YouTube</option>
                </BaseSelect>
                <div v-if="form.type === 'pdf'">
                    <BaseFileInput
                        label="رفع ملف PDF"
                        @update:modelValue="form.file = $event"
                        :error="form.errors.file"
                        accept=".pdf"
                    />
                </div>
                <div v-if="form.type === 'picture'">
                    <BaseFileInput
                        label="رفع صورة"
                        @update:modelValue="form.file = $event"
                        :error="form.errors.file"
                        accept="image/*"
                    />
                </div>
                <div v-if="form.type === 'youtube'">
                    <BaseInput
                        label="رابط يوتيوب"
                        v-model="form.youtube_link"
                        :error="form.errors.youtube_link"
                        placeholder="https://www.youtube.com/watch?v=..."
                    />
                </div>
                <BaseButton type="submit" :disabled="form.processing">
                    <!-- إضافة مادة -->
                    إضافة {{ type === 'youtube' ? 'محاضرة' : 'مقرر' }}
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

import { router, useForm } from '@inertiajs/vue3';

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
