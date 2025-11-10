<script setup>
import Card from '@/components/LayoutStructure/Card.vue';
// import CustomYoutubePlayer from '@/components/Misc/CustomYoutubePlayer.vue';

import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    material: Object,
});
</script>

<template>
    <div
        class="min-h-screen bg-gray-100 p-4 text-gray-800 sm:p-6 dark:bg-gray-900 dark:text-white"
    >
        <Head :title="material.title" />
        <div class="mx-auto max-w-5xl">
            <div class="mb-4">
                <Link
                    v-if="material.subject"
                    :href="route('subjects.show', material.subject.id)"
                    class="text-sm text-blue-500 hover:underline"
                >
                    &larr; {{ `العودة إلى ${material.subject.title}` }}
                </Link>
                <h1 class="mt-2 text-3xl font-bold">{{ material.title }}</h1>
            </div>

            <Card class="overflow-hidden p-0">
                <img
                    v-if="material.type === 'picture'"
                    :src="material.preview_url"
                    :alt="material.title"
                    class="h-auto w-full"
                />

                <!-- <div
                    v-else-if="material.type === 'youtube'"
                    class="aspect-video"
                >
                    <iframe
                        :src="material.embed_url"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen
                        class="h-full w-full"
                    ></iframe>
                </div> -->
                <div
                    v-else-if="material.type === 'youtube'"
                    class="aspect-video"
                >
                    <iframe
                        :src="material.embed_url"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        allowfullscreen
                        referrerpolicy="no-referrer-when-downgrade"
                        class="h-full w-full"
                    ></iframe>
                </div>

                <div
                    v-else-if="material.type === 'pdf'"
                    class="p-8 text-center"
                >
                    <p class="mb-4">هذه المادة عبارة عن مستند PDF.</p>
                    <a
                        :href="material.preview_url"
                        target="_blank"
                        class="inline-block rounded-lg bg-[#61CE70] px-6 py-3 font-semibold text-white transition hover:bg-[#4CAF60]"
                    >
                        تحميل / فتح PDF
                    </a>
                </div>
            </Card>

            <div v-if="material.key_points" class="mt-6">
                <h2 class="mb-2 text-xl font-semibold">النقاط الرئيسية</h2>
                <Card>
                    <p
                        class="whitespace-pre-wrap text-gray-600 dark:text-gray-400"
                    >
                        {{ material.key_points }}
                    </p>
                </Card>
            </div>
        </div>
    </div>
</template>
