<script setup>
import AdminLayout from '@/Pages/Admin/Layout.vue';
import BaseButton from '@/components/FormElements/BaseButton.vue';
import ConfirmDialog from '@/components/Misc/ConfirmDialog.vue';
import StudentForm from '@/components/Students/StudentForm.vue';
import { useTranslations } from '@/composables/useTranslations';
import { Head, router, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const { __ } = useTranslations();

defineOptions({ layout: AdminLayout });

const props = defineProps({
    student: Object,
    levels: Array,
});

const form = useForm({
    name: props.student.name,
    email: props.student.email,
    password: '',
    level_id: props.student.level_id,
    is_approved: props.student.is_approved,
});

const submit = () => {
    form.put(route('admin.students.update', { student: props.student.id }), {
        onSuccess: () => form.reset('password'),
    });
};

const showAdvanceConfirm = ref(false);

const currentLevel = computed(() => {
    return props.levels.find((l) => l.id === props.student.level_id);
});

const nextLevel = computed(() => {
    if (!currentLevel.value) {
        return props.levels.find((l) => l.order === 1);
    }
    const nextOrder = currentLevel.value.order + 1;
    return props.levels.find((l) => l.order === nextOrder);
});

const advanceStudent = () => {
    router.post(
        route('admin.students.advance', { user: props.student.id }),
        {},
        {
            preserveScroll: true,
            onFinish: () => (showAdvanceConfirm.value = false),
        },
    );
};
</script>

<template>
    <div>
        <Head :title="__('admin.edit_student', { name: student.name })" />
        <h1 class="mb-4 text-2xl font-bold">{{ __('admin.edit_student', { name: student.name }) }}</h1>
        <form @submit.prevent="submit">
            <StudentForm v-model="form" :levels="levels" />

            <div class="mt-4 flex justify-end gap-2">
                <BaseButton
                    type="button"
                    @click="showAdvanceConfirm = true"
                    :disabled="!nextLevel"
                    class="bg-blue-600 hover:bg-blue-700"
                    :title="advanceButtonTitle"
                >
                    {{ __('buttons.advance_level') }}
                </BaseButton>
                <BaseButton type="submit" :disabled="form.processing"> {{ __('buttons.update_student') }} </BaseButton>
            </div>
        </form>

        <ConfirmDialog
            :show="showAdvanceConfirm"
            :title="__('admin.advance_student_level')"
            :message="__('messages.advance_student_confirm', { name: student.name, level: nextLevel?.name })"
            @confirm="advanceStudent"
            @cancel="showAdvanceConfirm = false"
        />
    </div>
</template>
