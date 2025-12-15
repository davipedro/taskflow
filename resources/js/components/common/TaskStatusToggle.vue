<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { useTaskStatus } from '@/composables/useTaskStatus';
import { updateStatus } from '@/routes/tasks';
import type { Task } from '@/types';
import { router } from '@inertiajs/vue3';
import { ArrowRight } from 'lucide-vue-next';
import { computed } from 'vue';
import { toast } from 'vue-sonner';

interface Props {
    task: Task;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    change: [];
}>();

const { getStatusLabel, getNextStatusLabel, getNextStatus, canTransition: canTransitionStatus } = useTaskStatus();

const canTransition = computed(() => {
    return canTransitionStatus(props.task.status);
});

const nextStatusLabel = computed(() => {
    return getNextStatusLabel(props.task.status);
});

const handleNextStatus = () => {
    const nextStatus = getNextStatus(props.task.status);

    if (!nextStatus) return;

    router.patch(
        updateStatus(props.task.id).url,
        { status: nextStatus },
        {
            onSuccess: () => {
                toast.success(
                    `Status atualizado para ${getStatusLabel(nextStatus)}`,
                );
                emit('change');
            },
            onError: () => {
                toast.error('Erro ao atualizar o status da tarefa.');
            },
        },
    );
};
</script>

<template>
    <div class="flex items-center gap-2">
        <Button
            v-if="canTransition"
            variant="outline"
            size="icon"
            type="button"
            class="h-7 w-7 cursor-pointer transition-colors hover:border-primary hover:bg-primary hover:text-primary-foreground dark:hover:border-white dark:hover:bg-white dark:hover:text-black"
            @click.stop.prevent="handleNextStatus"
            :aria-label="`Avançar para ${nextStatusLabel}`"
            :title="`Avançar para ${nextStatusLabel}`"
        >
            <ArrowRight :size="14" />
        </Button>
    </div>
</template>
