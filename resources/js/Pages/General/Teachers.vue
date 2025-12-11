<template>
    <div
        class="container mx-auto max-w-7xl space-y-16 bg-neutral-50 px-4 py-16"
    >
        <!-- <Head title="الهيئة التدريسية" /> -->
        <SeoHead
            title="الهيئة التدريسية"
            description="تعرف على نخبة من الأساتذة والمتخصصين في الهيئة التدريسية لبرنامج بصيرة. علماء ومربون متميزون في العلوم الشرعية"
        />
        <JsonLd :data="structuredData" />

        <section class="relative px-6 py-16" dir="rtl">
            <div class="mb-14 text-center">
                <h2 class="mt-2 text-3xl font-bold text-green-600 md:text-4xl">
                    أعضاء الهيئة التدريسية في برنامج بصيرة
                </h2>
                <div class="mt-3 text-xl font-semibold text-[#4D88C7]">
                    ( أُمةً وسطاً )
                </div>
            </div>

            <div
                class="grid grid-cols-1 justify-items-center gap-x-10 gap-y-14 sm:grid-cols-2 lg:grid-cols-3"
            >
                <div
                    v-for="teacher in teachers"
                    :key="teacher.name"
                    class="relative flex min-h-[380px] transform flex-col items-center overflow-hidden rounded-3xl border border-gray-100 bg-[#FAFAF9] p-6 shadow-md transition duration-500 hover:scale-105 hover:shadow-xl"
                >
                    <div
                        class="relative flex h-44 w-44 items-center justify-center overflow-hidden rounded-full bg-white/80 p-2 shadow-sm ring-4 ring-green-600"
                    >
                        <div
                            class="absolute inset-0 rounded-full bg-gradient-to-tr from-green-400 to-green-200 opacity-0 transition-opacity duration-500 hover:opacity-40"
                        ></div>
                        <img
                            :src="teacher.photo_url"
                            :alt="teacher.name"
                            class="relative z-10 h-40 w-40 rounded-full object-cover"
                        />
                    </div>

                    <h2
                        class="mt-5 text-center text-lg font-bold text-green-700 md:text-xl"
                    >
                        {{ teacher.name }}
                    </h2>
                    <h5
                        class="mt-1 text-center text-sm font-medium text-gray-800 md:text-base"
                    >
                        {{ teacher.bio }}
                    </h5>
                </div>
            </div>
        </section>
    </div>
</template>

<script setup>
import OtherLayout from './OtherLayout.vue';
// import { Head } from '@inertiajs/vue3';
import SeoHead from '@/components/SeoHead.vue';
import JsonLd from '@/components/JsonLd.vue';

defineOptions({ layout: OtherLayout });

const props = defineProps({
    teachers: Array,
});

const structuredData = {
    '@context': 'https://schema.org',
    '@type': 'CollectionPage',
    name: 'الهيئة التدريسية - برنامج بصيرة',
    description:
        'تعرف على أعضاء الهيئة التدريسية في برنامج بصيرة للدراسات الإسلامية',
    url: 'https://basirahonline.com/teachers',
    about: {
        '@type': 'EducationalOrganization',
        name: 'برنامج بصيرة',
        url: 'https://basirahonline.com',
    },
    hasPart:
        props.teachers?.map((teacher) => ({
            '@type': 'Person',
            name: teacher.name,
            jobTitle: teacher.bio,
            image: teacher.photo_url,
            worksFor: {
                '@type': 'EducationalOrganization',
                name: 'برنامج بصيرة',
            },
        })) || [],
};
</script>
