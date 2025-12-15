import type { Task } from '@/types';

export function useTaskPriority() {
    const getPriorityLabel = (priority: Task['priority']) => {
        const labels = {
            LOW: 'Baixa',
            MEDIUM: 'Média',
            HIGH: 'Alta',
        };
        return labels[priority];
    };

    const getPriorityVariant = (priority: Task['priority']) => {
        const variants = {
            LOW: 'secondary',
            MEDIUM: 'default',
            HIGH: 'destructive',
        };
        return variants[priority] as 'default' | 'secondary' | 'destructive';
    };

    return {
        getPriorityLabel,
        getPriorityVariant,
    };
}
