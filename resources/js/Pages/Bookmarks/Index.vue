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
import { useTranslations } from '@/composables/useTranslations';
import { Link } from '@inertiajs/vue3';

const { __ } = useTranslations();


const props = defineProps({
    bookmarks: Object, // This is the paginated object from the controller
});
</script>

<template>
    <div>
        <Head :title="__('student.my_bookmarks')" />
        <SectionHeader :title="__('student.my_bookmarks')" />
        <p class="mb-6 text-sm text-gray-600 dark:text-gray-400">{{ __('student.bookmarks_description') }}</p>

        <div v-if="bookmarks.data.length" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <Card v-for="bookmark in bookmarks.data" :key="bookmark.id" class="flex flex-col">
                <div v-if="bookmark.material">
                    <a :href="bookmark.material.preview_url" target="_blank" class="block">
                        <img
                            v-if="bookmark.material.thumbnail_url"
                            :src="bookmark.material.thumbnail_url"
                            :alt="bookmark.material.title"
                            class="h-40 w-full rounded-t-lg object-cover"
                        />
                        <div v-else class="flex h-40 w-full items-center justify-center rounded-t-lg bg-gray-200 text-gray-400 dark:bg-gray-700">
                            {{ __('student.no_preview') }}
                        </div>
                    </a>
                    <div class="flex flex-grow flex-col p-4">
                        <h3 class="flex-grow font-medium">{{ bookmark.material.title }}</h3>
                        <p class="mb-2 text-xs text-gray-500">{{ __('student.in_subject', { title: bookmark.material.subject.title }) }}</p>
                        <div class="mt-2 flex items-center justify-between">
                            <BaseButton as="a" :href="bookmark.material.preview_url" target="_blank" class="bg-gray-600 text-sm hover:bg-gray-700">
                                {{ __('common.view') }}
                            </BaseButton>
                            <Link
                                :href="route('bookmarks.destroy', { material: bookmark.material.id })"
                                method="delete"
                                as="button"
                                class="text-sm text-red-500 hover:underline"
                                preserve-scroll
                            >
                                {{ __('student.remove') }}
                            </Link>
                        </div>
                    </div>
                </div>
            </Card>
        </div>
        <EmptyState v-else :message="__('student.no_bookmarks')" :sub="__('student.no_bookmarks_sub')" />

        <div class="mt-6">
            <Pagination :links="bookmarks.links" />
        </div>
    </div>
</template>
