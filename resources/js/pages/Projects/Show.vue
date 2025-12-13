<script setup lang="ts">
import Pagination from '@/components/common/Pagination.vue';
import ProjectEditDialog from '@/components/common/ProjectEditDialog.vue';
import TasksDataTable from '@/components/common/TasksDataTable.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import {
    Sheet,
    SheetContent,
    SheetDescription,
    SheetFooter,
    SheetHeader,
    SheetTitle,
} from '@/components/ui/sheet';
import { Skeleton } from '@/components/ui/skeleton';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { index, show } from '@/routes/projects';
import { store as storeTask } from '@/routes/projects/tasks';
import type { BreadcrumbItem, PaginatedResponse, Project, Task } from '@/types';
import { Deferred, Form, Head, router } from '@inertiajs/vue3';
import { ArrowLeft, Edit, Plus } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { toast } from 'vue-sonner';

interface Props {
    project: { data: Project };
    tasks?: PaginatedResponse<Task>;
}

const props = defineProps<Props>();

const project = computed(() => props.project.data);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Meus Projetos',
        href: index.url(),
    },
    {
        title: props.project.data.name,
        href: show(props.project.data.id).url,
    },
];

const headerTitle = 'Tarefa';

const isEditDialogOpen = ref(false);
const isCreateTaskSheetOpen = ref(false);
const selectedPriority = ref<Task['priority']>('MEDIUM');

const handleEdit = () => {
    isEditDialogOpen.value = true;
};

const handleEditSuccess = () => {
    router.reload({ only: ['project'] });
};

const handleBack = () => {
    router.visit(index.url());
};

const handleCreateTask = () => {
    isCreateTaskSheetOpen.value = true;
    selectedPriority.value = 'MEDIUM';
};

const handleCreateTaskSuccess = () => {
    isCreateTaskSheetOpen.value = false;
    toast.success('Tarefa criada com sucesso!');
    router.reload({ only: ['tasks', 'project'] });
};

const handleFormError = (errors: Record<string, string[]>) => {
    isCreateTaskSheetOpen.value = false;
    console.error('Form errors:', errors);
    toast.error(`Erro: ${Object.values(errors).flat().join(', ')}`);
    router.reload({ only: ['tasks', 'project'] });
};

const handleCloseCreateTaskSheet = () => {
    isCreateTaskSheetOpen.value = false;
};

const handleTaskRefresh = () => {
    router.reload({ only: ['tasks'] });
};
</script>

<template>
    <Head :title="project.name" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="flex items-center justify-between">
                <Heading :title="headerTitle" :description="project.name" />
                <div class="flex gap-2">
                    <Button variant="outline" @click="handleBack">
                        <ArrowLeft :size="16" />
                        Voltar
                    </Button>
                    <Button @click="handleEdit">
                        <Edit :size="16" />
                        Editar
                    </Button>
                </div>
            </div>
            <Card class="flex flex-1 flex-col p-6">
                <div class="flex flex-1 flex-col space-y-8">
                    <div class="grid gap-6 md:grid-cols-2">
                        <div class="space-y-4">
                            <div class="flex items-center gap-2">
                                <span
                                    class="text-sm font-medium text-muted-foreground"
                                    >Nome:</span
                                >
                                <span class="text-lg font-semibold">{{
                                    project.name
                                }}</span>
                            </div>
                            <div v-if="project.description">
                                <p
                                    class="mb-2 text-sm font-medium text-muted-foreground"
                                >
                                    Descrição:
                                </p>
                                <p class="text-base">
                                    {{ project.description }}
                                </p>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div class="flex items-center gap-2">
                                <span
                                    class="text-sm font-medium text-muted-foreground"
                                    >Cor:</span
                                >
                                <div
                                    class="h-6 w-6 rounded border"
                                    :style="{ backgroundColor: project.color }"
                                />
                                <span class="font-mono text-sm">{{
                                    project.color
                                }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span
                                    class="text-sm font-medium text-muted-foreground"
                                    >Total de Tarefas:</span
                                >
                                <span class="text-xl font-bold">{{
                                    project.tasks_count || 0
                                }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-1 flex-col space-y-3 border-t pt-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold">
                                Tarefas do Projeto
                            </h3>
                            <Button @click="handleCreateTask" size="sm">
                                <Plus :size="16" />
                                Nova Tarefa
                            </Button>
                        </div>
                        <Deferred data="tasks">
                            <div
                                class="flex flex-1 flex-col justify-between space-y-4"
                            >
                                <TasksDataTable
                                    :tasks="tasks?.data || []"
                                    @refresh="handleTaskRefresh"
                                />
                                <Pagination v-if="tasks" :resource="tasks" />
                            </div>

                            <template #fallback>
                                <div class="space-y-3 rounded-md border p-4">
                                    <Skeleton class="h-10 w-full" />
                                    <Skeleton class="h-10 w-full" />
                                    <Skeleton class="h-10 w-full" />
                                </div>
                            </template>
                        </Deferred>
                    </div>
                </div>
            </Card>
        </div>

        <ProjectEditDialog
            v-model:open="isEditDialogOpen"
            :project="project"
            @success="handleEditSuccess"
        />

        <Sheet v-model:open="isCreateTaskSheetOpen">
            <SheetContent>
                <SheetHeader>
                    <SheetTitle>Criar Nova Tarefa</SheetTitle>
                    <SheetDescription>
                        Preencha as informações da tarefa para o projeto "{{
                            project.name
                        }}"
                    </SheetDescription>
                </SheetHeader>

                <Form
                    :action="storeTask(project.id).url"
                    method="post"
                    class="flex flex-1 flex-col"
                    @success="handleCreateTaskSuccess"
                    @error="handleFormError"
                    v-slot="{ errors, processing }"
                >
                    <input
                        type="hidden"
                        name="project_id"
                        :value="project.id"
                    />
                    <input
                        type="hidden"
                        name="user_id"
                        :value="$page.props.auth.user.id"
                    />

                    <div class="flex-1 overflow-y-auto">
                        <div class="grid auto-rows-min gap-6 px-4">
                            <div class="space-y-2">
                                <Label for="task-title">Título *</Label>
                                <Input
                                    id="task-title"
                                    name="title"
                                    required
                                    placeholder="Ex: Implementar login"
                                />
                                <InputError :message="errors.title" />
                            </div>

                            <div class="space-y-2">
                                <Label for="task-description">Descrição</Label>
                                <Textarea
                                    id="task-description"
                                    name="description"
                                    placeholder="Descrição detalhada da tarefa"
                                    rows="4"
                                />
                                <InputError :message="errors.description" />
                            </div>

                            <div class="space-y-2">
                                <Label for="task-priority">Prioridade *</Label>
                                <input
                                    type="hidden"
                                    name="priority"
                                    :value="selectedPriority"
                                />
                                <Select v-model="selectedPriority">
                                    <SelectTrigger id="task-priority">
                                        <SelectValue
                                            placeholder="Selecione a prioridade"
                                        />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="LOW">Baixa</SelectItem>
                                        <SelectItem value="MEDIUM">Média</SelectItem>
                                        <SelectItem value="HIGH">Alta</SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError :message="errors.priority" />
                            </div>

                            <div class="space-y-2">
                                <Label for="task-deadline">Prazo</Label>
                                <Input
                                    id="task-deadline"
                                    name="deadline"
                                    type="date"
                                />
                                <InputError :message="errors.deadline" />
                            </div>
                        </div>
                    </div>

                    <SheetFooter class="flex-col gap-2">
                        <Button
                            type="submit"
                            :disabled="processing"
                            class="w-full"
                        >
                            Criar Tarefa
                        </Button>
                        <Button
                            type="button"
                            variant="outline"
                            class="w-full"
                            @click="handleCloseCreateTaskSheet"
                        >
                            Cancelar
                        </Button>
                    </SheetFooter>
                </Form>
            </SheetContent>
        </Sheet>
    </AppLayout>
</template>
