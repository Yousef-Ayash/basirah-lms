<script>
import AdminLayout from '@/Pages/Admin/Layout.vue';
import StudentLayout from '@/Pages/Student/Layout.vue';
import { usePage, Head } from '@inertiajs/vue3';

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
import SectionHeader from '@/components/LayoutStructure/SectionHeader.vue';
import EmptyState from '@/components/Misc/EmptyState.vue';
import { Link, router } from '@inertiajs/vue3';

const props = defineProps({
    bookmarks: Object,
});

function removeBookmark(materialId) {
    router.delete(route('bookmarks.destroy', { material: materialId }), {
        preserveScroll: true,
    });
}
</script>

<template>
    <div>
        <Head title="إشاراتي المرجعية" />
        <SectionHeader title="إشاراتي المرجعية" />
        <p class="mb-6 text-sm text-gray-600 dark:text-gray-400">
            جميع المواد المحفوظة في مكان واحد لسهولة الوصول إليها.
        </p>

        <div
            v-if="bookmarks.data.length"
            class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3"
        >
            <!-- inside <template> where bookmarks are listed, replace the card body with: -->
            <Card
                v-for="bookmark in bookmarks.data"
                :key="bookmark.id"
                class="flex flex-col"
            >
                <div v-if="bookmark.material">
                    <!-- make the thumbnail open material viewer (route to materials.show) -->
                    <a
                        :href="route('materials.show', bookmark.material.id)"
                        target="_blank"
                        class="block"
                    >
                        <!-- picture & youtube will have thumbnail_url -->
                        <img
                            v-if="
                                bookmark.material.thumbnail_url &&
                                bookmark.material.type !== 'pdf'
                            "
                            :src="bookmark.material.thumbnail_url"
                            :alt="bookmark.material.title"
                            class="h-40 w-full rounded-t-lg object-cover"
                        />

                        <!-- For PDFs explicitly show 'no preview available' box -->
                        <div
                            v-else-if="bookmark.material.type === 'pdf'"
                            class="flex h-40 w-full items-center justify-center rounded-t-lg bg-gray-200 text-gray-400 dark:bg-gray-700"
                        >
                            لا توجد معاينة متاحة
                        </div>

                        <!-- Fallback if no thumbnail (covers youtube or missing thumbnail) -->
                        <div
                            v-else
                            class="flex h-40 w-full items-center justify-center rounded-t-lg bg-gray-200 text-gray-400 dark:bg-gray-700"
                        >
                            لا توجد معاينة متاحة
                        </div>
                    </a>

                    <div class="flex flex-grow flex-col p-4">
                        <h3 class="flex-grow font-medium">
                            {{ bookmark.material.title }}
                        </h3>
                        <p class="mb-2 text-xs text-gray-500">
                            {{ `في مادة: ${bookmark.material.subject.title}` }}
                        </p>
                        <div class="mt-2 flex items-center justify-between">
                            <!-- View opens the MaterialViewer page (same as Subjects/Admin usage) -->
                            <BaseButton
                                as="a"
                                :href="
                                    route(
                                        'materials.show',
                                        bookmark.material.id,
                                    )
                                "
                                target="_blank"
                                class="bg-gray-600 text-sm hover:bg-gray-700"
                            >
                                عرض
                            </BaseButton>

                            <BaseButton
                                class="bg-red-600 text-sm hover:bg-red-700"
                                @click="removeBookmark(bookmark.material.id)"
                            >
                                إزالة
                            </BaseButton>
                        </div>
                    </div>
                </div>
            </Card>
        </div>
        <EmptyState
            v-else
            message="لم تقم بإضافة أي مواد إلى الإشارات المرجعية بعد."
            sub="انقر على أيقونة النجمة على أي مادة لحفظها هنا لوقت لاحق."
        />

        <div class="mt-6">
            <Pagination :links="bookmarks.links" />
        </div>
    </div>
</template>
