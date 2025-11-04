<script setup>
import AdminLayout from '@/Pages/Admin/Layout.vue';
import BaseButton from '@/components/FormElements/BaseButton.vue';
import { Head, useForm } from '@inertiajs/vue3';
import LevelForm from '@/components/Levels/LevelForm.vue';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    levels: Array,
});

const form = useForm({
    name: '',
    order: 0,
});

function submit() {
    form.post(route('admin.levels.store'));
}
</script>

<template>
    <div>
        <Head title="إنشاء مستوى" />
        <h1 class="mb-4 text-2xl font-bold">إنشاء مستوى</h1>

        <form @submit.prevent="submit">
            <LevelForm v-model="form" :levels="props.levels" />
            <div class="mt-4 flex justify-between">
                <BaseButton
                    as="a"
                    :href="route('admin.levels.index')"
                    class="bg-red-500 text-white hover:bg-red-600"
                >
                    إلغاء
                </BaseButton>
                <BaseButton type="submit" :disabled="form.processing">
                    إنشاء مستوى جديد
                </BaseButton>
            </div>
        </form>
    </div>
</template>
