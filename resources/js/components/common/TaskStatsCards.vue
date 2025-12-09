<script setup lang="ts">
import { Card, CardContent } from '@/components/ui/card';
import { Clock, ListTodo, CheckCircle2 } from 'lucide-vue-next';

interface Props {
    stats: {
        pending: number;
        in_progress: number;
        completed: number;
    };
}

defineProps<Props>();
</script>

<template>
    <div class="grid gap-4 md:grid-cols-3">
        <Card>
            <CardContent class="flex items-center justify-between">
                <div class="space-y-1">
                    <p class="text-sm text-muted-foreground">Pendentes</p>
                    <p class="text-3xl font-bold">{{ stats.pending }}</p>
                </div>
                <div class="rounded-full bg-secondary p-3">
                    <ListTodo class="text-secondary-foreground" :size="24" />
                </div>
            </CardContent>
        </Card>

        <Card>
            <CardContent class="flex items-center justify-between">
                <div class="space-y-1">
                    <p class="text-sm text-muted-foreground">Em Progresso</p>
                    <p class="text-3xl font-bold">{{ stats.in_progress }}</p>
                </div>
                <div class="rounded-full bg-primary p-3">
                    <Clock class="text-primary-foreground" :size="24" />
                </div>
            </CardContent>
        </Card>

        <Card>
            <CardContent class="flex items-center justify-between">
                <div class="space-y-1">
                    <p class="text-sm text-muted-foreground">Concluídas</p>
                    <p class="text-3xl font-bold">{{ stats.completed }}</p>
                </div>
                <div class="rounded-full bg-accent p-3">
                    <CheckCircle2 class="text-accent-foreground" :size="24" />
                </div>
            </CardContent>
        </Card>
    </div>
</template>
