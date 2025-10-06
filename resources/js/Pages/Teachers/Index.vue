<script setup>
import StudentLayout from '@/Pages/Student/Layout.vue';
import { Head } from '@inertiajs/vue3';
import Card from '@/components/LayoutStructure/Card.vue';
import SectionHeader from '@/components/LayoutStructure/SectionHeader.vue';

defineOptions({ layout: StudentLayout });

defineProps({
    teachers: Array,
});
</script>

<template>
    <div>
        <Head title="مدرّسونا" />
        <SectionHeader title="مدرّسونا" />

        <div
            class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4"
        >
            <Card v-for="teacher in teachers" :key="teacher.id">
                <div class="flex flex-col items-center">
                    <img
                        :src="teacher.photo_url"
                        :alt="teacher.name"
                        class="h-32 w-32 rounded-full object-cover"
                    />
                    <h3 class="mt-4 text-lg font-semibold">
                        {{ teacher.name }}
                    </h3>
                    <p
                        class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400"
                    >
                        {{ teacher.bio }}
                    </p>
                </div>
            </Card>
        </div>
    </div>
</template>
