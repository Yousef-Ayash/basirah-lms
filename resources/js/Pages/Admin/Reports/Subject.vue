<script setup>
import AdminLayout from '@/Pages/Admin/Layout.vue';
import Card from '@/components/LayoutStructure/Card.vue';
import SectionHeader from '@/components/LayoutStructure/SectionHeader.vue';

import { Link } from '@inertiajs/vue3';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    subject: Object,
    data: Object,
    pass_threshold: Number,
});
</script>

<template>
    <div>
        <Head :title="`تقرير الأداء لـ: ${subject.title}`" />
        <SectionHeader :title="`تقرير الأداء لـ: ${subject.title}`" />
        <Link
            :href="route('admin.reports.index')"
            class="mb-4 block text-sm text-blue-500 hover:underline"
            >&larr; العودة إلى التقارير</Link
        >

        <Card>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 text-right dark:bg-gray-700">
                        <tr>
                            <th class="p-2">عنوان الاختبار</th>
                            <th class="p-2">متوسط الدرجات</th>
                            <th class="p-2">إجمالي المحاولات</th>
                            <th class="p-2">
                                {{ `الناجحون (>= ${pass_threshold}%)` }}
                            </th>
                            <th class="p-2">نسبة النجاح</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="examReport in data"
                            :key="examReport.exam_id"
                            class="border-t dark:border-gray-700"
                        >
                            <td class="p-2">{{ examReport.exam.title }}</td>
                            <td class="p-2 font-semibold">
                                {{ examReport.avg_score || 'N/A' }}%
                            </td>
                            <td class="p-2">{{ examReport.attempts }}</td>
                            <td class="p-2">{{ examReport.passed }}</td>
                            <td class="p-2 font-semibold">
                                {{ examReport.pass_rate || 'N/A' }}%
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </Card>
    </div>
</template>
