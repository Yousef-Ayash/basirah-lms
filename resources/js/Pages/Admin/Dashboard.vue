<template>
    <div class="space-y-10">
        <!-- <Head title="لوحة تحكم المسؤول" />
        <SectionHeader title="لوحة تحكم المسؤول" /> -->

        <PageHeader title="لوحة تحكم المسؤول" />

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3">
            <Link
                v-for="card in summaryCards"
                :key="card.label"
                :href="card.href"
            >
                <Card
                    class="text-center transition hover:border-gray-300 dark:hover:border-gray-600"
                >
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ card.label }}
                    </p>
                    <h2 class="text-3xl font-bold text-[#61CE70]">
                        {{ card.value }}
                    </h2>
                </Card>
            </Link>
        </div>

        <SectionHeader title="الطلاب المسجلون حديثاً" />
        <Card v-if="recentStudents.length">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 text-right dark:bg-gray-700">
                        <tr>
                            <th class="p-2">الاسم</th>
                            <!-- <th class="p-2">البريد الإلكتروني</th> -->
                            <th class="p-2">رقم الموبايل</th>
                            <th class="p-2">تمت الموافقة</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="student in recentStudents"
                            :key="student.id"
                            class="border-t border-gray-200 dark:border-gray-700"
                        >
                            <td class="p-2">{{ student.name }}</td>
                            <!-- <td class="p-2">{{ student.email }}</td> -->
                            <td class="p-2">{{ student.phone }}</td>
                            <td class="p-2">
                                <span
                                    :class="
                                        student.is_approved
                                            ? 'text-green-500'
                                            : 'text-yellow-500'
                                    "
                                >
                                    {{ student.is_approved ? 'نعم' : 'لا' }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </Card>
        <EmptyState v-else message="لم يسجل أي طالب جديد مؤخراً." />

        <SectionHeader title="إجراءات سريعة" />
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <Link :href="route('admin.subjects.create')">
                <Card
                    class="h-full transition hover:border-gray-300 dark:hover:border-gray-600"
                >
                    <h3
                        class="text-lg font-semibold text-gray-900 dark:text-white"
                    >
                        إنشاء مادة جديدة
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        تنظيم مواضيع ودروس جديدة.
                    </p>
                </Card>
            </Link>
            <Link :href="route('admin.students.index')">
                <Card
                    class="h-full transition hover:border-gray-300 dark:hover:border-gray-600"
                >
                    <h3
                        class="text-lg font-semibold text-gray-900 dark:text-white"
                    >
                        إدارة الطلاب
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        الموافقة على حسابات الطلاب أو تعديلها أو استيرادها.
                    </p>
                </Card>
            </Link>
            <Link :href="route('admin.exams.index')">
                <Card
                    class="h-full transition hover:border-gray-300 dark:hover:border-gray-600"
                >
                    <h3
                        class="text-lg font-semibold text-gray-900 dark:text-white"
                    >
                        إدارة الاختبارات
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        إنشاء وإدارة محتوى الاختبارات والأسئلة.
                    </p>
                </Card>
            </Link>
        </div>
    </div>
</template>

<script setup>
import AdminLayout from '@/Pages/Admin/Layout.vue';
import Card from '@/components/LayoutStructure/Card.vue';
import SectionHeader from '@/components/LayoutStructure/SectionHeader.vue';
import EmptyState from '@/components/Misc/EmptyState.vue';
// import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';
import PageHeader from '@/components/LayoutStructure/PageHeader.vue';

// Set the layout for this page
defineOptions({ layout: AdminLayout });

// The dashboard now receives all its data directly from the controller via props
const props = defineProps({
    stats: Object,
    recentStudents: Array,
});

// The summary card data is now derived from the 'stats' prop
const summaryCards = computed(() => [
    {
        label: 'إجمالي الطلاب',
        value: props.stats.studentsCount,
        href: route('admin.students.index'),
    },
    {
        label: 'إجمالي المواد',
        value: props.stats.subjectsCount,
        href: route('admin.subjects.index'),
    },
    {
        label: 'الموافقات المعلقة',
        value: props.stats.pendingCount,
        href: route('admin.students.index', { is_approved: '0' }), // Link to a filtered view
    },
]);
</script>
