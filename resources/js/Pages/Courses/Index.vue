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
import BaseInput from '@/components/FormElements/BaseInput.vue';
import Card from '@/components/LayoutStructure/Card.vue';
import Pagination from '@/components/LayoutStructure/Pagination.vue';
// import SectionHeader from '@/components/LayoutStructure/SectionHeader.vue';
import EmptyState from '@/components/Misc/EmptyState.vue';
import { Link, router /*, Head*/ } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import PageHeader from '@/components/LayoutStructure/PageHeader.vue';

const props = defineProps({
    courses: Object,
    filters: Object,
});

const search = ref(props.filters.q || '');

// Auto-submit search on typing
watch(search, (val) => {
    router.get(
        route('courses.index'),
        { q: val },
        { preserveState: true, replace: true },
    );
});
</script>

<template>
    <div>
        <!-- <Head title="المقررات الدراسية" />
        <SectionHeader title="المقررات الدراسية" />

        <Card class="mb-6">
            <div class="flex flex-col gap-4 md:flex-row">
                <BaseInput
                    v-model="search"
                    placeholder="ابحث عن مقرر..."
                    class="w-full md:w-1/2"
                />
            </div>
        </Card> -->

        <PageHeader title="المقررات الدراسية">
            <template #filters>
                <div class="flex flex-col gap-4 md:flex-row">
                    <BaseInput
                        v-model="search"
                        placeholder="ابحث عن مقرر..."
                        class="w-full md:w-1/2"
                    />
                </div>
            </template>
        </PageHeader>

        <div
            v-if="courses.data.length"
            class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3"
        >
            <Link
                v-for="course in courses.data"
                :key="course.id"
                :href="route('subjects.list', { course_id: course.id })"
            >
                <Card
                    class="flex h-full flex-col transition hover:-translate-y-1 hover:shadow-lg"
                >
                    <div
                        class="mb-3 flex h-48 w-full items-center justify-center overflow-hidden rounded-md bg-gray-200 dark:bg-gray-700"
                    >
                        <img
                            v-if="course.cover_url"
                            :src="course.cover_url"
                            :alt="course.title"
                            class="h-full w-full object-cover transition-transform duration-500 hover:scale-110"
                        />
                        <span v-else class="text-sm text-gray-400"
                            >لا توجد صورة</span
                        >
                    </div>

                    <div class="flex-grow">
                        <h2 class="mb-2 text-xl font-bold text-[#61CE70]">
                            {{ course.title }}
                        </h2>
                        <p
                            class="line-clamp-2 text-sm text-gray-600 dark:text-gray-400"
                        >
                            {{ course.description }}
                        </p>
                    </div>

                    <div class="mt-2 text-xs text-gray-500">
                        <span>{{ course.subjects_count }} مواد دراسية</span>
                    </div>
                </Card>
            </Link>
        </div>

        <EmptyState
            v-else
            message="لم يتم العثور على مقررات."
            sub="حاول تغيير مصطلحات البحث."
        />

        <div class="mt-6">
            <Pagination :links="courses.links" />
        </div>
    </div>
</template>
