import type { Task } from '@/types';

export function useDateFormatter() {
    const formatDate = (date: string | null) => {
        if (!date) return '-';

        const dateOnly = date.split('T')[0];
        const [year, month, day] = dateOnly.split('-');

        return `${day}/${month}/${year}`;
    };

    const formatDateLong = (date: string | null) => {
        if (!date) return '-';

        const dateOnly = date.split('T')[0];
        const [year, month, day] = dateOnly.split('-');
        const localDate = new Date(Number(year), Number(month) - 1, Number(day));

        return localDate.toLocaleDateString('pt-BR', {
            day: '2-digit',
            month: 'long',
            year: 'numeric',
        });
    };

    const formatDateTime = (date: string | null) => {
        if (!date) return '-';

        const [datePart, timePart] = date.split('T');
        const [year, month, day] = datePart.split('-');
        const [hour, minute] = timePart.split(':');
        const localDate = new Date(
            Number(year),
            Number(month) - 1,
            Number(day),
            Number(hour),
            Number(minute),
        );

        return localDate.toLocaleDateString('pt-BR', {
            day: '2-digit',
            month: 'long',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        });
    };

    const formatDateForInput = (date: string | null) => {
        if (!date) return '';
        return date.split('T')[0];
    };

    const isOverdue = (task: Task) => {
        if (!task.deadline || task.status === 'COMPLETED') return false;

        const today = new Date();
        today.setHours(0, 0, 0, 0);

        const deadline = new Date(task.deadline);
        deadline.setHours(0, 0, 0, 0);

        return deadline < today;
    };

    return {
        formatDate,
        formatDateLong,
        formatDateTime,
        formatDateForInput,
        isOverdue,
    };
}
