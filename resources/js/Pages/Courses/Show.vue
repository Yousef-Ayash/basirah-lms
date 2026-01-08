<script>
import { usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Pages/Admin/Layout.vue';
import StudentLayout from '@/Pages/Student/Layout.vue';

export default {
    layout: (h, page) => {
        const user = usePage().props.auth.user;
        const isAdmin = user.roles?.some((role) => role.name === 'admin');
        return h(isAdmin ? AdminLayout : StudentLayout, [page]);
    },
};
</script>

<script setup>
import Card from '@/components/LayoutStructure/Card.vue';
import SectionHeader from '@/components/LayoutStructure/SectionHeader.vue';
import EmptyState from '@/components/Misc/EmptyState.vue';
import { Link, Head } from '@inertiajs/vue3';

defineProps({
    course: Object,
    subjects: Array,
});
</script>

<template>
    <div>
        <Head :title="course.title" />

        <div class="mb-6">
            <Link
                :href="route('subjects.index')"
                class="mb-2 block text-sm text-blue-500 hover:underline"
            >
                ← العودة إلى قائمة المقررات
            </Link>
            <SectionHeader :title="course.title" />
            <p
                v-if="course.description"
                class="max-w-3xl text-gray-600 dark:text-gray-400"
            >
                {{ course.description }}
            </p>
        </div>

        <div
            v-if="subjects.length"
            class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3"
        >
            <Link
                v-for="subject in subjects"
                :key="subject.id"
                :href="route('subjects.show', subject.id)"
            >
                <Card
                    class="flex h-full flex-col transition hover:-translate-y-1 hover:shadow-lg"
                >
                    <div
                        class="mb-3 flex h-40 w-full items-center justify-center overflow-hidden rounded-md bg-gray-100 dark:bg-gray-700"
                    >
                        <img
                            v-if="subject.cover_url"
                            :src="subject.cover_url"
                            :alt="subject.title"
                            class="h-full w-full object-cover"
                        />
                        <span v-else class="text-3xl">📚</span>
                    </div>

                    <h3
                        class="mb-1 text-lg font-semibold text-gray-900 dark:text-white"
                    >
                        {{ subject.title }}
                    </h3>

                    <div
                        class="mt-auto flex flex-col gap-1 pt-3 text-xs text-gray-500 dark:text-gray-400"
                    >
                        <span v-if="subject.teacher">
                            المدرس: {{ subject.teacher.name }}
                        </span>
                        <div class="mt-1 flex gap-3">
                            <span>{{ subject.materials_count }} مقررات</span>
                            <span>{{ subject.exams_count }} اختبارات</span>
                        </div>
                    </div>
                </Card>
            </Link>
        </div>

        <EmptyState v-else message="لا توجد مواد في هذه المقرر حالياً." />
    </div>
</template>
