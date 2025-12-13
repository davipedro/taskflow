<script setup lang="ts">
import TaskStatsCards from '@/components/common/TaskStatsCards.vue';
import TasksDataTable from '@/components/common/TasksDataTable.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem, type Task } from '@/types';
import { Head, router } from '@inertiajs/vue3';

interface Props {
    stats: {
        pending: number;
        in_progress: number;
        completed: number;
    };
    recentTasks: Task[];
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

const handleRefresh = () => {
    router.reload({ only: ['stats', 'recentTasks'] });
};
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-4 md:p-6">
            <TaskStatsCards :stats="stats" />

            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold tracking-tight">
                            Tarefas Recentes
                        </h2>
                        <p class="text-sm text-muted-foreground">
                            Suas 5 tarefas mais recentes
                        </p>
                    </div>
                </div>

                <TasksDataTable :tasks="recentTasks" @refresh="handleRefresh" />
            </div>
        </div>
    </AppLayout>
</template>
