<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import Pagination from '@/components/common/Pagination.vue';
import TasksDataTable from '@/components/common/TasksDataTable.vue';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';
import { index } from '@/routes/tasks';
import type { BreadcrumbItem, PaginatedResponse, Task } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Filter, X } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

interface Props {
    tasks: PaginatedResponse<Task>;
    filters: {
        status: string | null;
        priority: string | null;
        sort_by: string;
        sort_order: string;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Minhas Tarefas',
        href: index.url(),
    },
];

const headerTitle = 'Minhas Tarefas';
const headerDescription = 'Gerencie todas as suas tarefas';

// Filter state
const selectedStatus = ref<string>(props.filters.status || 'all');
const selectedPriority = ref<string>(props.filters.priority || 'all');
const selectedSortBy = ref<string>(props.filters.sort_by || 'created_at');
const selectedSortOrder = ref<string>(props.filters.sort_order || 'desc');

// Computed
const hasActiveFilters = computed(() => {
    return selectedStatus.value !== 'all' || selectedPriority.value !== 'all';
});

const totalTasks = computed(() => props.tasks.meta.total || 0);

// Methods
const applyFilters = () => {
    const params: Record<string, string> = {};

    if (selectedStatus.value && selectedStatus.value !== 'all') {
        params.status = selectedStatus.value;
    }

    if (selectedPriority.value && selectedPriority.value !== 'all') {
        params.priority = selectedPriority.value;
    }

    if (selectedSortBy.value) {
        params.sort_by = selectedSortBy.value;
    }

    if (selectedSortOrder.value) {
        params.sort_order = selectedSortOrder.value;
    }

    router.visit(index.url(), {
        data: params,
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    selectedStatus.value = 'all';
    selectedPriority.value = 'all';
    selectedSortBy.value = 'created_at';
    selectedSortOrder.value = 'desc';

    router.visit(index.url(), {
        preserveState: true,
        preserveScroll: true,
    });
};

const handleTaskRefresh = () => {
    router.reload({ only: ['tasks'] });
};

watch([selectedStatus, selectedPriority, selectedSortBy, selectedSortOrder], () => {
    applyFilters();
});
</script>

<template>
    <Head :title="headerTitle" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="flex items-center justify-between">
                <Heading :title="headerTitle" :description="headerDescription" />
                <div class="flex items-center gap-2">
                    <span class="text-sm text-muted-foreground">
                        {{ totalTasks }} {{ totalTasks === 1 ? 'tarefa' : 'tarefas' }}
                    </span>
                </div>
            </div>

            <!-- Filters Section -->
            <Card class="p-4">
                <div class="flex flex-col gap-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <Filter :size="16" class="text-muted-foreground" />
                            <h3 class="text-sm font-semibold">Filtros</h3>
                        </div>
                        <Button
                            v-if="hasActiveFilters"
                            variant="ghost"
                            size="sm"
                            @click="clearFilters"
                        >
                            <X :size="16" />
                            Limpar Filtros
                        </Button>
                    </div>

                    <div class="grid gap-4 md:grid-cols-4">
                        <!-- Status Filter -->
                        <div class="space-y-2">
                            <Label for="filter-status">Status</Label>
                            <Select v-model="selectedStatus">
                                <SelectTrigger id="filter-status">
                                    <SelectValue placeholder="Todos os status" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">Todos</SelectItem>
                                    <SelectItem value="PENDING">Pendente</SelectItem>
                                    <SelectItem value="IN_PROGRESS">Em Progresso</SelectItem>
                                    <SelectItem value="COMPLETED">Concluída</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>

                        <!-- Priority Filter -->
                        <div class="space-y-2">
                            <Label for="filter-priority">Prioridade</Label>
                            <Select v-model="selectedPriority">
                                <SelectTrigger id="filter-priority">
                                    <SelectValue placeholder="Todas as prioridades" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">Todas</SelectItem>
                                    <SelectItem value="LOW">Baixa</SelectItem>
                                    <SelectItem value="MEDIUM">Média</SelectItem>
                                    <SelectItem value="HIGH">Alta</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>

                        <!-- Sort By Filter -->
                        <div class="space-y-2">
                            <Label for="filter-sort-by">Ordenar por</Label>
                            <Select v-model="selectedSortBy">
                                <SelectTrigger id="filter-sort-by">
                                    <SelectValue placeholder="Ordenar por" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="created_at">Data de Criação</SelectItem>
                                    <SelectItem value="deadline">Prazo</SelectItem>
                                    <SelectItem value="priority">Prioridade</SelectItem>
                                    <SelectItem value="title">Título</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>

                        <!-- Sort Order Filter -->
                        <div class="space-y-2">
                            <Label for="filter-sort-order">Ordem</Label>
                            <Select v-model="selectedSortOrder">
                                <SelectTrigger id="filter-sort-order">
                                    <SelectValue placeholder="Ordem" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="desc">Decrescente</SelectItem>
                                    <SelectItem value="asc">Crescente</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                    </div>
                </div>
            </Card>

            <!-- Tasks Table -->
            <Card class="flex flex-1 flex-col p-6">
                <div class="flex flex-1 flex-col justify-between space-y-4">
                    <TasksDataTable
                        :tasks="tasks.data || []"
                        @refresh="handleTaskRefresh"
                    />
                    <Pagination v-if="tasks" :resource="tasks" />
                </div>
            </Card>
        </div>
    </AppLayout>
</template>
