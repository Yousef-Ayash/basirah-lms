<template>
    <div class="flex min-h-screen flex-col bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-white">
        <div v-if="viewingAsStudent" class="bg-yellow-400 p-2 text-center text-sm text-black">
            {{ __('admin.viewing_as_student_banner') }}
            <Link :href="route('admin.view-as-admin')" class="font-bold underline">{{ __('admin.return_to_admin') }}</Link>
        </div>

        <NavBar :links="navLinks" />
        <main class="mx-auto w-full max-w-7xl flex-grow px-4 py-6">
            <Alert v-if="flashSuccess" :message="flashSuccess" type="success" class="mb-4" />
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
    { label: __('common.dashboard'), to: route('dashboard') },
    { label: __('common.subjects'), to: route('admin.subjects.index') },
    { label: __('common.exams'), to: route('admin.exams.index') },
    { label: __('common.students'), to: route('admin.students.index') },
    { label: __('common.marks'), to: route('admin.marks.index') },
    { label: __('common.reports'), to: route('admin.reports.index') },
    { label: __('common.logs'), to: route('admin.logs.exam.index') },
]);

const studentLinks = computed(() => [
    { label: __('common.dashboard'), to: route('dashboard') },
    { label: __('common.subjects'), to: route('subjects.index') },
    { label: __('student.my_marks_and_attempts'), to: route('attempts.index') },
    { label: __('student.my_bookmarks'), to: route('bookmarks.index') },
]);

const navLinks = computed(() => (viewingAsStudent.value ? studentLinks.value : adminLinks.value));
</script>
