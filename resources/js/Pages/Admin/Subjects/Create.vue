<template>
    <div>
        <Head title="إنشاء مادة جديدة" />
        <h1 class="mb-4 text-2xl font-bold">إنشاء مادة جديدة</h1>

        <!-- <form @submit.prevent="submit" enctype="multipart/form-data">
            <SubjectForm v-model="form" :levels="props.levels" />

            <div class="mt-4 flex justify-between">
                <div class="justify-start">
                    <BaseButton
                        as="a"
                        :href="route('admin.subjects.index')"
                        class="bg-red-500 text-white hover:bg-red-600"
                    >
                        إلغاء
                    </BaseButton>
                </div>
                <div class="justify-end">
                    <BaseButton type="submit" :disabled="form.processing">
                        إنشاء
                    </BaseButton>
                </div>
            </div>
        </form> -->
        <form @submit.prevent="storeSubject" enctype="multipart/form-data">
            <!-- SubjectForm expects v-model="form" and will emit update:modelValue -->
            <SubjectForm
                :modelValue="form"
                :levels="levels"
                @update:modelValue="(partial) => Object.assign(form, partial)"
            />

            <div class="mt-4 flex justify-between">
                <div class="justify-start">
                    <BaseButton
                        as="a"
                        :href="route('admin.subjects.index')"
                        class="bg-red-500 text-white hover:bg-red-600"
                    >
                        إلغاء
                    </BaseButton>
                </div>
                <div class="justify-end">
                    <BaseButton type="submit" :disabled="form.processing">
                        إنشاء
                    </BaseButton>
                </div>
            </div>
        </form>
        <pre style="white-space: pre-wrap; font-size: 12px">
Parent form state:
{{
                JSON.stringify(
                    {
                        title: form.title,
                        level_id: form.level_id,
                        cover: form.cover
                            ? { name: form.cover.name, size: form.cover.size }
                            : null,
                    },
                    null,
                    2,
                )
            }}
</pre
        >
    </div>
</template>

<script setup>
import AdminLayout from '@/Pages/Admin/Layout.vue';
import BaseButton from '@/components/FormElements/BaseButton.vue';
import SubjectForm from '@/components/Subjects/SubjectForm.vue';

import { Head, useForm } from '@inertiajs/vue3';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    levels: Array,
});

const form = useForm({
    title: '',
    description: '',
    level_id: 0,
    cover: null,
});

// const submit = () => {
//     form.post(route('admin.subjects.store'));
// };

function storeSubject() {
    // debug - ensure file is attached
    // open console and watch the object before submit
    // You can remove the console logs after verifying.
    // Note: in dev only - do not leave heavy logging in production.
    // eslint-disable-next-line no-console
    console.log(
        '[Create] before submit -> form.cover =',
        form.cover,
        'isFile?',
        form.cover instanceof File,
    );
    console.log(
        '[Create] full form snapshot:',
        JSON.parse(
            JSON.stringify({
                title: form.title,
                level_id: form.level_id,
                // cover cannot be JSONified; show name if present:
                cover: form.cover
                    ? { name: form.cover.name, size: form.cover.size }
                    : null,
            }),
        ),
    );

    // send as multipart automatically if any value is a File
    form.post(route('admin.subjects.store'), {
        onError: (errs) => {
            // debug errors in console
            // eslint-disable-next-line no-console
            console.log('validation errors', errs);
        },
        onSuccess: () => {
            // optional success handling
        },
    });
}

import { nextTick } from 'vue';

async function submit() {
    await nextTick(); // allow any last emissions to flush
    form.post(route('admin.subjects.store'));
}
</script>
