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
import BaseButton from '@/components/FormElements/BaseButton.vue';
import Card from '@/components/LayoutStructure/Card.vue';
import Pagination from '@/components/LayoutStructure/Pagination.vue';
import TabGroup from '@/components/LayoutStructure/TabGroup.vue';
import EmptyState from '@/components/Misc/EmptyState.vue';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    subject: Object,
    materials: Object, // Paginated materials
    vid_materials: Object,
    exams: Array,
    bookmarkedMaterialIds: Array,
});

console.log(props.materials);
console.log(props.vid_materials);

// 1. Create a local, reactive copy of the bookmarked IDs.
//    This allows us to update the UI instantly.
const localBookmarkedIds = ref([...props.bookmarkedMaterialIds]);

// 2. The `isBookmarked` function now checks our local copy.
const isBookmarked = (materialId) => {
    return localBookmarkedIds.value.includes(materialId);
};

// 3. The refactored toggle function.
const toggleBookmark = (material) => {
    const isCurrentlyBookmarked = isBookmarked(material.id);

    if (isCurrentlyBookmarked) {
        // --- Handle Un-bookmarking ---
        // Optimistically remove the ID from our local list
        localBookmarkedIds.value = localBookmarkedIds.value.filter(
            (id) => id !== material.id,
        );

        // Send the DELETE request to the server
        router.delete(route('bookmarks.destroy', { material: material.id }), {
            preserveScroll: true,
            // If the server request fails, add the ID back to revert the change
            onError: () => localBookmarkedIds.value.push(material.id),
        });
    } else {
        // --- Handle Bookmarking ---
        // Optimistically add the ID to our local list
        localBookmarkedIds.value.push(material.id);

        // Send the POST request to the server with the correct data
        router.post(
            route('bookmarks.store'),
            { material_id: material.id },
            {
                preserveScroll: true,
                // If the server request fails, remove the ID to revert the change
                onError: () => {
                    localBookmarkedIds.value = localBookmarkedIds.value.filter(
                        (id) => id !== material.id,
                    );
                },
            },
        );
    }
};
</script>

<template>
    <div>
        <Head :title="subject.title" />
        <div class="mb-6">
            <img
                v-if="subject.cover_image"
                :src="subject.cover_image"
                alt="Subject Cover"
                class="mb-4 max-h-64 w-full rounded-lg object-cover"
            />
            <h1 class="mb-2 text-3xl font-bold">{{ subject.title }}</h1>
            <p class="text-gray-600 dark:text-gray-400">
                {{ subject.description }}
            </p>
        </div>

        <TabGroup :tabs="['مواد', 'محاضرات', 'الاختبارات']" v-slot="{ active }">
            <div v-show="active === 0" class="space-y-4">
                <div
                    v-if="materials.data.length"
                    class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3"
                >
                    <Card
                        v-for="material in materials.data"
                        :key="material.id"
                        class="flex flex-col"
                    >
                        <a
                            :href="route('materials.show', material.id)"
                            target="_blank"
                            class="block"
                        >
                            <img
                                v-if="material.thumbnail_url"
                                :src="material.thumbnail_url"
                                class="h-40 w-full rounded-t-lg object-cover"
                            />
                            <div
                                v-else
                                class="flex h-40 w-full items-center justify-center rounded-t-lg bg-gray-200 text-gray-400 dark:bg-gray-700"
                            >
                                لا توجد معاينة متاحة
                            </div>
                        </a>
                        <div class="flex flex-grow flex-col p-4">
                            <h3 class="flex-grow font-medium">
                                {{ material.title }}
                            </h3>
                            <div class="mt-2 flex items-center justify-between">
                                <span
                                    class="rounded bg-gray-200 px-2 py-1 text-xs font-semibold uppercase dark:bg-gray-700"
                                    >{{ material.type }}</span
                                >
                                <button
                                    @click="toggleBookmark(material)"
                                    class="rounded-full p-1 hover:bg-gray-200 dark:hover:bg-gray-700"
                                >
                                    <svg
                                        v-if="isBookmarked(material.id)"
                                        class="h-5 w-5 text-yellow-500"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                                        ></path>
                                    </svg>
                                    <svg
                                        v-else
                                        class="h-5 w-5 text-gray-400"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"
                                        ></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </Card>
                </div>
                <div v-else class="mt-6">
                    <EmptyState
                        message="لم تتم إضافة أي مواد لهذه المادة بعد."
                    />
                </div>
                <div class="mt-6">
                    <Pagination :links="materials.links" />
                </div>
            </div>

            <div v-show="active === 1" class="space-y-4">
                <div
                    v-if="vid_materials.data.length"
                    class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3"
                >
                    <Card
                        v-for="material in vid_materials.data"
                        :key="material.id"
                        class="flex flex-col"
                    >
                        <a
                            :href="route('materials.show', material.id)"
                            target="_blank"
                            class="block"
                        >
                            <img
                                v-if="material.thumbnail_url"
                                :src="material.thumbnail_url"
                                class="h-40 w-full rounded-t-lg object-cover"
                            />
                            <div
                                v-else
                                class="flex h-40 w-full items-center justify-center rounded-t-lg bg-gray-200 text-gray-400 dark:bg-gray-700"
                            >
                                لا توجد معاينة متاحة
                            </div>
                        </a>
                        <div class="flex flex-grow flex-col p-4">
                            <h3 class="flex-grow font-medium">
                                {{ material.title }}
                            </h3>
                            <div class="mt-2 flex items-center justify-between">
                                <span
                                    class="rounded bg-gray-200 px-2 py-1 text-xs font-semibold uppercase dark:bg-gray-700"
                                    >{{ material.type }}</span
                                >
                                <button
                                    @click="toggleBookmark(material)"
                                    class="rounded-full p-1 hover:bg-gray-200 dark:hover:bg-gray-700"
                                >
                                    <svg
                                        v-if="isBookmarked(material.id)"
                                        class="h-5 w-5 text-yellow-500"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                                        ></path>
                                    </svg>
                                    <svg
                                        v-else
                                        class="h-5 w-5 text-gray-400"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"
                                        ></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </Card>
                </div>
                <div v-else class="mt-6">
                    <EmptyState
                        message="لم تتم إضافة أي مواد لهذه المادة بعد."
                    />
                </div>
                <div class="mt-6">
                    <Pagination :links="materials.links" />
                </div>
            </div>

            <div v-show="active === 2" class="space-y-4">
                <div v-if="exams.length">
                    <Card
                        v-for="exam in exams"
                        :key="exam.id"
                        class="flex flex-col p-4 sm:flex-row sm:items-center sm:justify-between"
                    >
                        <div>
                            <h3 class="text-lg font-medium">
                                {{ exam.title }}
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                المدة المسموح بها
                                {{ exam.time_limit_minutes }}
                                دق
                            </p>
                        </div>
                        <BaseButton
                            as="a"
                            :href="route('exams.show', exam.id)"
                            class="mt-2 sm:mt-0"
                        >
                            عرض الاختبار
                        </BaseButton>
                    </Card>
                </div>
                <EmptyState
                    v-else
                    message="لا توجد اختبارات متاحة لهذه المادة بعد."
                />
            </div>
        </TabGroup>
    </div>
</template>
