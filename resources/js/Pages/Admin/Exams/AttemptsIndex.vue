<template>
    <div>
        <Head :title="`محاولات لـ: ${exam.title}`" />
        <h1 class="mb-4 text-2xl font-bold">
            {{ `محاولات لـ: ${exam.title}` }}
        </h1>
        <Card>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 text-right dark:bg-gray-700">
                        <tr>
                            <th class="p-2">الطالب</th>
                            <th class="p-2">المحاولة رقم</th>
                            <th class="p-2">الدرجة</th>
                            <th class="p-2">وقت التسليم</th>
                            <th class="p-2">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="attempt in attempts.data"
                            :key="attempt.id"
                            class="border-t dark:border-gray-700"
                        >
                            <td class="p-2">{{ attempt.user.name }}</td>
                            <td class="p-2">{{ attempt.attempt_number }}</td>
                            <td class="p-2 font-semibold">
                                {{ attempt.score }}%
                            </td>
                            <td class="p-2">
                                {{
                                    new Date(
                                        attempt.submitted_at,
                                    ).toLocaleString()
                                }}
                            </td>
                            <td class="p-2">
                                <BaseButton
                                    as="a"
                                    :href="
                                        route('admin.exams.attempts.show', {
                                            exam: exam.id,
                                            attempt: attempt.id,
                                        })
                                    "
                                >
                                    عرض التفاصيل
                                </BaseButton>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </Card>
        <div class="mt-6">
            <Pagination :links="attempts.links" />
        </div>
    </div>
</template>

<script setup>
import AdminLayout from '@/Pages/Admin/Layout.vue';
import BaseButton from '@/components/FormElements/BaseButton.vue';
import Card from '@/components/LayoutStructure/Card.vue';
import Pagination from '@/components/LayoutStructure/Pagination.vue';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    exam: Object,
    attempts: Object,
});
</script>
