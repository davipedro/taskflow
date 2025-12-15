<script setup lang="ts">
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { updatePriority } from '@/routes/tasks';
import type { Task } from '@/types';
import { router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { toast } from 'vue-sonner';

interface Props {
    task: Task;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    change: [];
}>();

const selectedPriority = ref<Task['priority']>(props.task.priority);

watch(
    () => props.task.priority,
    (newPriority) => {
        selectedPriority.value = newPriority;
    },
);

const getPriorityLabel = (priority: Task['priority']) => {
    const labels = {
        LOW: 'Baixa',
        MEDIUM: 'Média',
        HIGH: 'Alta',
    };
    return labels[priority];
};

const handlePriorityChange = (priority: Task['priority']) => {
    if (priority === props.task.priority) return;

    router.patch(
        updatePriority(props.task.id).url,
        { priority },
        {
            preserveScroll: true,
            onSuccess: () => {
                toast.success(
                    `Prioridade atualizada para ${getPriorityLabel(priority)}`,
                );
                emit('change');
            },
            onError: () => {
                selectedPriority.value = props.task.priority;
                toast.error('Erro ao atualizar a prioridade da tarefa.');
            },
        },
    );
};
</script>

<template>
    <div class="w-[110px]" @click.stop>
        <Select v-model="selectedPriority" @update:model-value="handlePriorityChange">
            <SelectTrigger class="h-7 text-xs">
                <SelectValue />
            </SelectTrigger>
            <SelectContent>
                <SelectItem class="text-secondary-foreground hover:bg-secondary focus:bg-secondary" value="LOW">Baixa</SelectItem>
                <SelectItem class="text-yellow-300 data-[highlighted]:bg-yellow-700" value="MEDIUM">Média</SelectItem>
                <SelectItem class="text-red-500 data-[highlighted]:bg-red-500 data-[highlighted]:text-white" value="HIGH">Alta</SelectItem>
            </SelectContent>
        </Select>
    </div>
</template>
