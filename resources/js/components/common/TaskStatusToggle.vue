<script setup lang="ts">
import { Button } from '@/components/ui/button';
import type { Task } from '@/types';
import { router } from '@inertiajs/vue3';
import { ArrowRight } from 'lucide-vue-next';
import { computed } from 'vue';
import { updateStatus } from '@/routes/tasks';
import { toast } from 'vue-sonner';

interface Props {
    task: Task;
}

const props = defineProps<Props>();

const getStatusLabel = (status: Task['status']) => {
    const labels = {
        PENDING: 'Pendente',
        IN_PROGRESS: 'Em Progresso',
        COMPLETED: 'Concluída',
    };
    return labels[status];
};

const getNextStatusLabel = (status: Task['status']) => {
    const nextLabels = {
        PENDING: 'Em Progresso',
        IN_PROGRESS: 'Concluída',
        COMPLETED: null,
    };
    return nextLabels[status];
};

const canTransition = computed(() => {
    return props.task.status !== 'COMPLETED';
});

const nextStatusLabel = computed(() => {
    return getNextStatusLabel(props.task.status);
});

const handleNextStatus = () => {
    const nextStatus = {
        PENDING: 'IN_PROGRESS',
        IN_PROGRESS: 'COMPLETED',
        COMPLETED: null,
    }[props.task.status];

    if (!nextStatus) return;

    router.patch(updateStatus(props.task.id).url, { status: nextStatus }, {
        onSuccess: () => {
            toast.success(`Status atualizado para ${getStatusLabel(nextStatus)}`);
        },
        onError: () => {
            toast.error('Erro ao atualizar o status da tarefa.');
        },
    });
};
</script>

<template>
    <div class="flex items-center gap-2">
        <Button variant="outline" disabled>
            {{ getStatusLabel(task.status) }}
        </Button>
        <Button
            v-if="canTransition"
            variant="outline"
            size="icon"
            type="button"
            @click.stop.prevent="handleNextStatus"
            :aria-label="`Avançar para ${nextStatusLabel}`"
            :title="`Avançar para ${nextStatusLabel}`"
        >
            <ArrowRight :size="16" />
        </Button>
    </div>
</template>
