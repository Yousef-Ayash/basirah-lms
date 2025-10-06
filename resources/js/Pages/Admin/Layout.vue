<template>
    <div
        class="flex min-h-screen flex-col bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-white"
    >
        <div
            v-if="viewingAsStudent"
            class="bg-yellow-400 p-2 text-center text-sm text-black"
        >
            أنت تعرض التطبيق حالياً كطالب،
            <Link
                :href="route('admin.view-as-admin')"
                class="font-bold underline"
                >العودة إلى العرض كمسؤول.</Link
            >
        </div>

        <NavBar :links="navLinks" />
        <main class="mx-auto w-full max-w-7xl flex-grow px-4 py-6">
            <Alert
                v-if="flashSuccess"
                :message="flashSuccess"
                type="success"
                class="mb-4"
            />
            <slot />
        </main>
        <Footer />
    </div>
</template>

<script setup>
import Footer from '@/components/LayoutStructure/Footer.vue';
import NavBar from '@/components/LayoutStructure/NavBar.vue';
import Alert from '@/components/Misc/Alert.vue';
import { useTranslations } from '@/composables/useTranslations';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const { __ } = useTranslations();
const page = usePage();

const flashSuccess = computed(() => page.props.flash.success);
const viewingAsStudent = computed(() => page.props.auth.viewingAsStudent);

const adminLinks = computed(() => [
    { label: 'لوحة التحكم', to: route('dashboard') },
    { label: 'المواد الدراسية', to: route('admin.subjects.index') },
    { label: 'الاختبارات', to: route('admin.exams.index') },
    { label: 'الطلاب', to: route('admin.students.index') },
    { label: 'الدرجات', to: route('admin.marks.index') },
    { label: 'التقارير', to: route('admin.reports.index') },
    { label: 'السجلات', to: route('admin.logs.exam.index') },
    { label: 'المدرسون', to: route('admin.teachers.index') },
    { label: 'المستويات', to: route('admin.levels.index') },
]);

const studentLinks = computed(() => [
    { label: 'لوحة التحكم', to: route('dashboard') },
    { label: 'المواد الدراسية', to: route('subjects.index') },
    { label: 'درجاتي ومحاولاتي', to: route('attempts.index') },
    { label: 'إشاراتي المرجعية', to: route('bookmarks.index') },
]);

const navLinks = computed(() =>
    viewingAsStudent.value ? studentLinks.value : adminLinks.value,
);
</script>
