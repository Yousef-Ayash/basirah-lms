<template>
    <Card class="space-y-4">
        <BaseInput
            label="عنوان الاختبار"
            v-model="modelValue.title"
            :error="modelValue.errors.title"
            required
        />
        <BaseTextarea
            label="الوصف (اختياري)"
            v-model="modelValue.description"
            :error="modelValue.errors.description"
            rows="3"
        />
        <BaseSelect
            label="المادة"
            v-model="modelValue.subject_id"
            :error="modelValue.errors.subject_id"
            required
        >
            <option disabled :value="null">
                اختر مادة
            </option>
            <option
                v-for="subject in subjects"
                :key="subject.id"
                :value="subject.id"
            >
                {{ subject.title }}
            </option>
        </BaseSelect>
        <div class="grid gap-4 sm:grid-cols-3">
            <BaseInput
                label="الوقت المحدد (بالدقائق)"
                type="number"
                v-model="modelValue.time_limit_minutes"
                :error="modelValue.errors.time_limit_minutes"
                placeholder="مثال: 60"
            />
            <BaseInput
                label="أقصى عدد للمحاولات"
                type="number"
                v-model="modelValue.max_attempts"
                :error="modelValue.errors.max_attempts"
                required
            />
            <div>
                <BaseInput
                    label="المسافة بين المحاولات (بالايام)"
                    type="number"
                    v-model="modelValue.distance_between_attempts"
                    :error="modelValue.errors.distance_between_attempts"
                    required
                />
                <p class="mt-1 text-xs text-gray-500">
                    اتركه فارغاً أو 0 لإلغاء قيود المسافة بين كل محاولة تقديم.
                </p>
            </div>
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <BaseInput
                    label="عدد الأسئلة المراد عرضها"
                    type="number"
                    v-model="modelValue.questions_to_display"
                    :error="modelValue.errors.questions_to_display"
                    :placeholder="`${'مثال:'} 20`"
                />
                <p class="mt-1 text-xs text-gray-500">
                    اتركه فارغاً أو 0 لإظهار كافة الأسئلة من بنك الأسئلة.
                </p>
            </div>
            <BaseInput
                label="العلامة الكاملة"
                type="number"
                v-model="modelValue.full_mark"
                :error="modelValue.errors.full_mark"
                :placeholder="`${'مثال:'} 60`"
            />
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            <BaseInput
                label="يفتح في (اختياري)"
                type="datetime-local"
                :model-value="formatDateForInput(modelValue.open_at)"
                @input="modelValue.open_at = $event.target.value"
                :error="modelValue.errors.open_at"
            />
            <BaseInput
                label="يغلق في (اختياري)"
                type="datetime-local"
                :model-value="formatDateForInput(modelValue.close_at)"
                @input="modelValue.close_at = $event.target.value"
                :error="modelValue.errors.close_at"
            />
        </div>
        <div class="space-y-2">
            <div class="flex items-center space-x-2">
                <input
                    type="checkbox"
                    id="review_allowed"
                    v-model="modelValue.review_allowed"
                    class="rounded accent-[#61CE70]"
                />
                <label for="review_allowed">السماح للطلاب بمراجعة محاولاتهم</label>
            </div>
            <div class="flex items-center space-x-2">
                <input
                    type="checkbox"
                    id="show_answers_after_close"
                    v-model="modelValue.show_answers_after_close"
                    class="rounded accent-[#61CE70]"
                />
                <label for="show_answers_after_close">إظهار الإجابات الصحيحة بعد إغلاق الاختبار</label>
            </div>
        </div>
    </Card>
</template>

<script setup>
import BaseInput from '@/components/FormElements/BaseInput.vue';
import BaseSelect from '@/components/FormElements/BaseSelect.vue';
import BaseTextarea from '@/components/FormElements/BaseTextarea.vue';
import Card from '@/components/LayoutStructure/Card.vue';

defineProps({
    modelValue: Object, // Inertia form object
    subjects: Array,
});

defineEmits(['update:modelValue']);

// Helper to format datetime-local input
const formatDateForInput = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toISOString().slice(0, 16);
};
</script>
