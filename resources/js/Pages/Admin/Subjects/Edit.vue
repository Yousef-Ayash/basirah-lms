<template>
    <div>
        <!-- <Head :title="`تعديل المادة: ${subject.title}`" />
        <h1 class="mb-4 text-2xl font-bold">
            {{ `تعديل المادة: ${subject.title}` }}
        </h1> -->
        <PageHeader :title="`تعديل المادة: ${subject.title}`" />

        <TabGroup :tabs="tabLabels" v-slot="{ active }">
            <div v-show="active === 0">
                <form
                    @submit.prevent="updateSubjectDetails"
                    enctype="multipart/form-data"
                >
                    <SubjectForm
                        :modelValue="form"
                        :levels="levels"
                        :teachers="teachers"
                        :courses="courses"
                        @update:modelValue="
                            (partial) => Object.assign(form, partial)
                        "
                    />
                    <div class="mt-4 flex justify-between">
                        <BaseButton
                            as="a"
                            :href="route('admin.subjects.index')"
                            class="bg-red-500 text-white hover:bg-red-600"
                        >
                            إلغاء
                        </BaseButton>
                        <BaseButton type="submit" :disabled="form.processing">
                            حفظ التغييرات
                        </BaseButton>
                    </div>
                </form>
            </div>

            <div v-show="active === 1" class="space-y-6">
                <AddMaterialForm :subject-id="subject.id" />

                <SectionHeader title="الملفات والمحاضرات الموجودة" />
                <Card v-if="subject.materials.length">
                    <ul class="divide-y dark:divide-gray-700">
                        <li
                            v-for="material in subject.materials"
                            :key="material.id"
                            class="flex items-center justify-between p-2"
                        >
                            <span
                                >{{ material.title }} ({{
                                    material.type
                                }})</span
                            >
                            <div class="flex gap-2">
                                <BaseButton
                                    as="a"
                                    :href="route('materials.show', material.id)"
                                    target="_blank"
                                    class="bg-gray-500 hover:bg-gray-600"
                                >
                                    عرض
                                </BaseButton>
                                <Link
                                    :href="
                                        route(
                                            'admin.materials.destroy',
                                            material.id,
                                        )
                                    "
                                    method="delete"
                                    as="button"
                                    class="inline-flex items-center justify-center rounded-lg bg-red-500 px-4 py-2 font-semibold text-white transition hover:bg-red-600"
                                    preserve-scroll
                                >
                                    حذف
                                </Link>
                            </div>
                        </li>
                    </ul>
                </Card>
                <EmptyState
                    v-else
                    message="لم تتم إضافة أي ملفات لهذه المادة بعد."
                />
            </div>

            <div v-show="active === 2" class="space-y-6">
                <SectionHeader title="الاختبارات الخاصة بالمادة">
                    <template #action>
                        <BaseButton
                            as="a"
                            :href="
                                route('admin.exams.create', {
                                    subject_id: subject.id,
                                })
                            "
                        >
                            + اختبار جديد
                        </BaseButton>
                    </template>
                </SectionHeader>

                <Card v-if="subject.exams.length">
                    <ul class="divide-y dark:divide-gray-700">
                        <li
                            v-for="exam in subject.exams"
                            :key="exam.id"
                            class="flex items-center justify-between p-2"
                        >
                            <span>{{ exam.title }}</span>
                            <div class="flex gap-2">
                                <BaseButton
                                    as="a"
                                    :href="route('admin.exams.show', exam.id)"
                                    >إدارة الأسئلة</BaseButton
                                >
                                <BaseButton
                                    as="a"
                                    :href="route('admin.exams.edit', exam.id)"
                                    class="bg-blue-500 hover:bg-blue-600"
                                >
                                    تعديل التفاصيل
                                </BaseButton>
                            </div>
                        </li>
                    </ul>
                </Card>
                <EmptyState
                    v-else
                    message="لا توجد اختبارات متاحة لهذه المادة بعد."
                />
            </div>
        </TabGroup>
    </div>
</template>

<script setup>
import AdminLayout from '@/Pages/Admin/Layout.vue';
import BaseButton from '@/components/FormElements/BaseButton.vue';
import Card from '@/components/LayoutStructure/Card.vue';
import SectionHeader from '@/components/LayoutStructure/SectionHeader.vue';
import TabGroup from '@/components/LayoutStructure/TabGroup.vue';
import AddMaterialForm from '@/components/Materials/AddMaterialForm.vue';
import EmptyState from '@/components/Misc/EmptyState.vue';
import SubjectForm from '@/components/Subjects/SubjectForm.vue';
import PageHeader from '@/components/LayoutStructure/PageHeader.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    subject: Object,
    levels: Array,
    teachers: Array,
    courses: Array,
});

// Initialize form with proper types and include cover_url for existing image
const form = useForm({
    _method: 'PUT',
    title: props.subject.title || '',
    description: props.subject.description || '',
    level_id: Number(props.subject.level_id) || 0,
    teacher_id: Number(props.subject.teacher_id) || 0,
    course_id: Number(props.subject.course_id) || 0,
    cover: null,
    cover_url: props.subject.cover || null, // Keep reference to existing cover
    remove_cover: false,
});

const tabLabels = computed(() => [
    'التفاصيل',
    'الملفات والمحاضرات',
    'الاختبارات',
]);

const updateSubjectDetails = () => {
    form.post(route('admin.subjects.update', props.subject.id), {
        onError: (errs) => {
            console.log('validation errors', errs);
        },
    });
};
</script>
