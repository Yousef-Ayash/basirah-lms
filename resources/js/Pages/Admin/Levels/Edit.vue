<script setup>
import AdminLayout from '@/Pages/Admin/Layout.vue';
import BaseButton from '@/components/FormElements/BaseButton.vue';
import { Head, useForm } from '@inertiajs/vue3';
import LevelForm from '@/components/Levels/LevelForm.vue';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    level: Object,
});

const form = useForm({
    name: props.level.name,
    order: 0,
});

function submit() {
    form.put(route('admin.levels.update', { level: props.level.id }));
}
</script>

<template>
    <div>
        <Head :title="`تعديل المستوى: ${level.name}`" />
        <h1 class="mb-4 text-2xl font-bold">تعديل المستوى</h1>

        <form @submit.prevent="submit">
            <LevelForm v-model="form" :levels="props.level" />
            <div class="mt-4 flex justify-between">
                <BaseButton
                    as="a"
                    :href="route('admin.levels.index')"
                    class="bg-red-500 text-white hover:bg-red-600"
                >
                    إلغاء
                </BaseButton>
                <BaseButton type="submit" :disabled="form.processing"
                    >حفظ التعديلات</BaseButton
                >
            </div>
        </form>
    </div>
</template>
