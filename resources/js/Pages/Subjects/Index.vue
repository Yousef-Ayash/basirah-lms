<script>
import { usePage, Head } from '@inertiajs/vue3';
import AdminLayout from '@/Pages/Admin/Layout.vue';
import StudentLayout from '@/Pages/Student/Layout.vue';

export default {
    layout: (h, page) => {
        const user = usePage().props.auth.user;
        const isAdmin = user.roles?.some((role) => role.name === 'admin');
        const LayoutComponent = isAdmin ? AdminLayout : StudentLayout;
        return h(LayoutComponent, [page]);
    },
};
</script>

<script setup>
import BaseInput from '@/components/FormElements/BaseInput.vue';
import Card from '@/components/LayoutStructure/Card.vue';
import Pagination from '@/components/LayoutStructure/Pagination.vue';
import SectionHeader from '@/components/LayoutStructure/SectionHeader.vue';
import EmptyState from '@/components/Misc/EmptyState.vue';
import { Link, router, Head } from '@inertiajs/vue3';
import { reactive, ref, watch } from 'vue';
import BaseSelect from '@/components/FormElements/BaseSelect.vue';

const props = defineProps({
    subjects: Object,
    levels: Array,
    filters: Object,
    selectedCourse: Object,
});

const filters = reactive({
    q: props.filters.q || '',
    level_id: props.filters.level_id || '',
    course_id: props.filters.course_id || '',
});

watch(
    filters,
    (newFilters) => {
        router.get(route('subjects.list'), newFilters, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
    },
    { deep: true },
);
</script>

<template>
    <div>
        <Head title="المواد الدراسية" />

        <div v-if="selectedCourse" class="mb-2">
            <Link
                :href="route('courses.index')"
                class="text-sm text-blue-500 hover:underline"
            >
                ← العودة إلى قائمة المقررات
            </Link>
        </div>

        <SectionHeader
            :title="
                selectedCourse
                    ? `مواد مقرر: ${selectedCourse.title}`
                    : 'المواد الدراسية'
            "
        />

        <Card class="mb-6">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <BaseInput
                    v-model="filters.q"
                    placeholder="البحث بالعنوان أو الوصف..."
                />
                <BaseSelect v-model="filters.level_id">
                    <option value="">كل المستويات</option>
                    <option
                        v-for="level in levels"
                        :key="level.id"
                        :value="level.id"
                    >
                        {{ level.name }}
                    </option>
                </BaseSelect>
            </div>
        </Card>

        <div
            v-if="subjects.data.length"
            class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3"
        >
            <Link
                v-for="subject in subjects.data"
                :key="subject.id"
                :href="route('subjects.show', subject.id)"
            >
                <Card
                    class="flex h-full flex-col transition hover:-translate-y-1 hover:shadow-lg"
                >
                    <div
                        class="mb-3 flex h-40 w-full items-center justify-center rounded-md bg-gray-200 dark:bg-gray-700"
                    >
                        <img
                            v-if="subject.cover_url"
                            :src="subject.cover_url"
                            alt="Subject Cover"
                            class="h-full w-full rounded-md object-cover"
                        />
                        <span v-else class="text-sm text-gray-400"
                            >لا توجد صورة</span
                        >
                    </div>

                    <h2 class="mb-1 text-lg font-semibold text-[#61CE70]">
                        {{ subject.title }}
                    </h2>
                    <div class="m-1 text-gray-500 dark:text-gray-300">
                        <span>{{ subject.level.name }}</span>
                        |
                        <span>{{ subject.teacher.name }}</span>
                    </div>
                    <p
                        class="line-clamp-2 text-sm text-gray-600 dark:text-gray-400"
                    >
                        {{ subject.description }}
                    </p>
                    <div class="mt-2 text-xs text-gray-500">
                        <span>{{ subject.materials_count }} منهاج</span>
                        |
                        <span>{{ subject.exams_count }} اختبارات</span>
                    </div>
                </Card>
            </Link>
        </div>

        <EmptyState
            v-else
            message="لا توجد مواد دراسية متاحة."
            sub="حاول تغيير البحث أو التحقق لاحقاً."
        />

        <div class="mt-6">
            <Pagination :links="subjects.links" />
        </div>
    </div>
</template>
