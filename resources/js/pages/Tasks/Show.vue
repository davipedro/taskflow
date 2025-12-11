<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import type { Task } from '@/types';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { ArrowLeft } from 'lucide-vue-next';

interface Props {
    task: Task;
}

defineProps<Props>();

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

const formatDate = (date: string | null) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('pt-BR', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    });
};

const formatDateTime = (date: string | null) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('pt-BR', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const isOverdue = (task: Task) => {
    if (!task.deadline || task.status === 'COMPLETED') return false;
    return new Date(task.deadline) < new Date();
};

const goBack = () => {
    router.visit('/tasks');
};
</script>

<template>
    <AppLayout>
        <Head :title="`Tarefa: ${task.data.title}`" />

        <div class="container mx-auto max-w-4xl py-8">
            <Button variant="ghost" @click="goBack" class="mb-6">
                <ArrowLeft :size="16" class="mr-2" />
                Voltar para Tarefas
            </Button>

            <Card>
                <CardHeader>
                    <CardTitle class="text-3xl">{{ task.data.title }}</CardTitle>
                    <CardDescription v-if="task.data.description" class="mt-3 text-base">
                        {{ task.data.description }}
                    </CardDescription>
                </CardHeader>

                <CardContent class="space-y-6">
                    <div class="grid grid-cols-2 gap-6 md:grid-cols-4">
                        <div class="space-y-2">
                            <h4 class="text-sm font-medium text-muted-foreground">Status</h4>
                            <Badge :variant="getStatusVariant(task.data.status)" class="text-sm">
                                {{ getStatusLabel(task.data.status) }}
                            </Badge>
                        </div>

                        <div class="space-y-2">
                            <h4 class="text-sm font-medium text-muted-foreground">Prioridade</h4>
                            <Badge :variant="getPriorityVariant(task.data.priority)" class="text-sm">
                                {{ getPriorityLabel(task.data.priority) }}
                            </Badge>
                        </div>

                        <div class="space-y-2">
                            <h4 class="text-sm font-medium text-muted-foreground">Prazo</h4>
                            <p
                                class="text-sm font-medium"
                                :class="{
                                    'text-destructive': isOverdue(task.data),
                                }"
                            >
                                {{ formatDate(task.data.deadline) }}
                            </p>
                            <p v-if="isOverdue(task.data)" class="text-xs text-destructive">
                                Atrasada
                            </p>
                        </div>

                        <div class="space-y-2">
                            <h4 class="text-sm font-medium text-muted-foreground">
                                {{ task.data.status === 'COMPLETED' ? 'Concluída em' : 'Criada em' }}
                            </h4>
                            <p class="text-sm">
                                {{
                                    task.data.status === 'COMPLETED' && task.data.completed_at
                                        ? formatDateTime(task.data.completed_at)
                                        : formatDateTime(task.data.created_at)
                                }}
                            </p>
                        </div>
                    </div>

                    <div v-if="task.data.project" class="space-y-2 border-t pt-6">
                        <h4 class="text-sm font-medium text-muted-foreground">Projeto</h4>
                        <div class="flex items-center gap-3">
                            <div
                                class="h-4 w-4 rounded-full"
                                :style="{ backgroundColor: task.data.project.color }"
                            />
                            <p class="text-base font-medium">{{ task.data.project.name }}</p>
                        </div>
                        <p v-if="task.data.project.description" class="text-sm text-muted-foreground">
                            {{ task.data.project.description }}
                        </p>
                    </div>

                    <div class="border-t pt-6">
                        <div class="flex flex-col gap-2 text-xs text-muted-foreground sm:flex-row sm:justify-between">
                            <p>Criada em: {{ formatDateTime(task.data.created_at) }}</p>
                            <p>Última atualização: {{ formatDateTime(task.data.updated_at) }}</p>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
