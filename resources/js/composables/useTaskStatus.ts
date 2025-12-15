import type { Task } from '@/types';

export function useTaskStatus() {
    const getStatusLabel = (status: Task['status']) => {
        const labels = {
            PENDING: 'Pendente',
            IN_PROGRESS: 'Em Progresso',
            COMPLETED: 'Concluída',
        };
        return labels[status];
    };

    const getStatusVariant = (status: Task['status']) => {
        const variants = {
            PENDING: 'secondary',
            IN_PROGRESS: 'default',
            COMPLETED: 'outline',
        };
        return variants[status] as 'default' | 'secondary' | 'outline';
    };

    const getNextStatus = (status: Task['status']): Task['status'] | null => {
        const nextStatus = {
            PENDING: 'IN_PROGRESS' as const,
            IN_PROGRESS: 'COMPLETED' as const,
            COMPLETED: null,
        };
        return nextStatus[status];
    };

    const getNextStatusLabel = (status: Task['status']) => {
        const nextLabels = {
            PENDING: 'Em Progresso',
            IN_PROGRESS: 'Concluída',
            COMPLETED: null,
        };
        return nextLabels[status];
    };

    const canTransition = (status: Task['status']) => {
        return status !== 'COMPLETED';
    };

    return {
        getStatusLabel,
        getStatusVariant,
        getNextStatus,
        getNextStatusLabel,
        canTransition,
    };
}
