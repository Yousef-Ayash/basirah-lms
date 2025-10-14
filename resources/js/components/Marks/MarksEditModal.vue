<template>
    <Modal @close="close" title="Edit Official Mark">
        <div class="space-y-4">
            <div>
                <label class="block text-sm">Marks</label>
                <BaseInput
                    v-model="form.marks"
                    type="number"
                    placeholder="Numeric mark"
                />
                <div v-if="errors.marks" class="text-sm text-red-600">
                    {{ errors.marks }}
                </div>
            </div>

            <div>
                <label class="block text-sm">Notes (optional)</label>
                <BaseTextarea v-model="form.notes" rows="3" />
            </div>

            <div class="flex items-center gap-4">
                <label class="flex items-center gap-2">
                    <input type="checkbox" v-model="form.released" />
                    <span class="text-sm">Release to student</span>
                </label>
            </div>

            <div class="flex justify-end gap-2">
                <BaseButton variant="ghost" @click="close">Cancel</BaseButton>
                <BaseButton :loading="processing" @click="submit"
                    >Save</BaseButton
                >
            </div>
        </div>
    </Modal>
</template>

<script setup>
import Modal from '@/components/LayoutStructure/Modal.vue';
import BaseButton from '@/components/FormElements/BaseButton.vue';
import BaseInput from '@/components/FormElements/BaseInput.vue';
import BaseTextarea from '@/components/FormElements/BaseTextarea.vue';
import { ref, watch } from 'vue';
import { Inertia } from '@inertiajs/inertia';

const props = defineProps({
    attemptId: [Number, String],
    marksReport: Object, // optional
});

const emit = defineEmits(['close']);

const form = ref({
    marks: props.marksReport?.marks ?? '',
    notes: props.marksReport?.notes ?? '',
    released: !!props.marksReport?.released,
});

const errors = ref({});
const processing = ref(false);

watch(
    () => props.marksReport,
    (v) => {
        form.value = {
            marks: v?.marks ?? '',
            notes: v?.notes ?? '',
            released: !!v?.released,
        };
    },
);

function close() {
    emit('close', { refreshed: false });
}

async function submit() {
    processing.value = true;
    errors.value = {};

    try {
        if (props.marksReport && props.marksReport.id) {
            await Inertia.put(
                route('admin.marks.update', props.marksReport.id),
                {
                    marks: form.value.marks,
                    notes: form.value.notes,
                    released: form.value.released ? 1 : 0,
                },
                { preserveState: true },
            );
        } else {
            await Inertia.post(
                route('admin.marks.store'),
                {
                    attempt_id: props.attemptId,
                    marks: form.value.marks,
                    notes: form.value.notes,
                    released: form.value.released ? 1 : 0,
                },
                { preserveState: true },
            );
        }

        emit('close', { refreshed: true });
    } catch (e) {
        // Inertia handles validation; fallback:
        errors.value = e?.response?.data?.errors ?? {};
    } finally {
        processing.value = false;
    }
}
</script>
