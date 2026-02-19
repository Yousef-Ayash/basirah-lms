<script setup>
import AdminLayout from '@/Pages/Admin/Layout.vue';
import BaseButton from '@/components/FormElements/BaseButton.vue';
import PageHeader from '@/components/LayoutStructure/PageHeader.vue';
import MarkForm from '@/components/Marks/MarkForm.vue';

import { Head, useForm } from '@inertiajs/vue3';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    mark: Object,
    users: Array,
    exams: Array,
});

const form = useForm({
    user_id: props.mark.user_id,
    exam_id: props.mark.exam_id,
    marks: props.mark.marks,
    notes: props.mark.notes,
});

const submit = () => {
    form.put(route('admin.marks.update', props.mark.id));
};
</script>

<template>
    <div>
        <!-- <Head :title="`تعديل درجة لـ ${mark.user.name}`" />
        <h1 class="mb-4 text-2xl font-bold">تعديل الدرجة</h1> -->

        <PageHeader :title="`تعديل درجة لـ ${mark.user.name}`" />

        <form @submit.prevent="submit">
            <MarkForm v-model="form" :users="users" :exams="exams" />
            <div class="mt-4 flex justify-between">
                <BaseButton
                    as="a"
                    :href="route('admin.marks.index')"
                    class="bg-red-500 text-white hover:bg-red-600"
                >
                    إلغاء
                </BaseButton>
                <BaseButton type="submit" :disabled="form.processing"
                    >تحديث الدرجة</BaseButton
                >
            </div>
        </form>
    </div>
</template>
