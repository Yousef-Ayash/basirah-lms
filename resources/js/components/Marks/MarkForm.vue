<script setup>
import { computed } from 'vue';
import BaseInput from '@/components/FormElements/BaseInput.vue';
import BaseSelect from '@/components/FormElements/BaseSelect.vue';
import BaseTextarea from '@/components/FormElements/BaseTextarea.vue';
import Card from '@/components/LayoutStructure/Card.vue';

const props = defineProps({
    modelValue: Object, // The Inertia form object
    users: Array,
    exams: Array,
});

defineEmits(['update:modelValue']);

const selectedExam = computed(() => {
    if (!props.modelValue.exam_id || !props.exams) {
        return null;
    }
    // Use == to handle potential type mismatch ('1' vs 1) from the select input
    return props.exams.find((exam) => exam.id == props.modelValue.exam_id);
});

const marksLabel = computed(() => {
    if (selectedExam.value) {
        return `الدرجات (من ${selectedExam.value.full_mark})`;
    }
    return 'الدرجات';
});

const maxMark = computed(() => {
    return selectedExam.value ? selectedExam.value.full_mark : 100;
});
</script>

<template>
    <Card class="space-y-4">
        <BaseSelect
            label="الطالب"
            v-model="modelValue.user_id"
            :error="modelValue.errors.user_id"
            required
        >
            <option disabled :value="null">اختر طالباً</option>
            <option v-for="user in users" :key="user.id" :value="user.id">
                <!-- {{ user.name }} ({{ user.email }}) -->
                {{ user.name }} ({{ user.phone }})
            </option>
        </BaseSelect>

        <BaseSelect
            label="الاختبار"
            v-model="modelValue.exam_id"
            :error="modelValue.errors.exam_id"
            required
        >
            <option disabled :value="null">اختر اختباراً</option>
            <option v-for="exam in exams" :key="exam.id" :value="exam.id">
                {{ exam.title }}
            </option>
        </BaseSelect>

        <BaseInput
            :label="marksLabel"
            type="number"
            min="0"
            :max="maxMark"
            v-model="modelValue.marks"
            :error="modelValue.errors.marks"
            required
        />

        <BaseTextarea
            label="ملاحظات (اختياري)"
            v-model="modelValue.notes"
            :error="modelValue.errors.notes"
            rows="3"
        />
    </Card>
</template>
