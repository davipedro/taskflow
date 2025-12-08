<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import {
    Table,
    TableBody,
    TableCell,
    TableEmpty,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog';
import { Button } from '@/components/ui/button';
import type { Task } from '@/types';
import { Form, router } from '@inertiajs/vue3';
import { Trash2, Pencil } from 'lucide-vue-next';
import { ref } from 'vue';
import { toast } from 'vue-sonner';
import { destroy as destroyTask, update } from '@/routes/tasks';
import { Sheet, SheetContent, SheetDescription, SheetFooter, SheetHeader, SheetTitle } from '@/components/ui/sheet';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import InputError from '@/components/InputError.vue';
import TaskStatusToggle from '@/components/common/TaskStatusToggle.vue';

interface Props {
    tasks: Task[];
}

defineProps<Props>();

const emit = defineEmits<{
    refresh: [];
}>();

const taskToDelete = ref<Task | null>(null);
const showDeleteDialog = ref(false);

const isEditTaskSheetOpen = ref(false);
const editingTask = ref<Task | null>(null);
const selectedPriority = ref<Task['priority']>('MEDIUM');

const handleEditClick = (task: Task) => {
    editingTask.value = task;
    selectedPriority.value = task.priority;
    isEditTaskSheetOpen.value = true;
};

const handleDeleteClick = (task: Task) => {
    taskToDelete.value = task;
    showDeleteDialog.value = true;
};

const handleDelete = () => {
    if (!taskToDelete.value) return;

    router.delete(destroyTask(taskToDelete.value.id).url, {
        onSuccess: () => {
            emit('refresh');
            showDeleteDialog.value = false;
            taskToDelete.value = null;
            toast.success('Tarefa excluída com sucesso!');
        },
        onError: () => {
            toast.error('Ocorreu um erro ao excluir a tarefa.');
        },
    });
};

const handleEditTaskSuccess = () => {
    isEditTaskSheetOpen.value = false;
    editingTask.value = null;
    toast.success('Tarefa atualizada com sucesso!');
    emit('refresh');
};

const handleCloseEditTaskSheet = () => {
    isEditTaskSheetOpen.value = false;
    editingTask.value = null;
};

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
    return new Date(date).toLocaleDateString('pt-BR');
};

const isOverdue = (task: Task) => {
    if (!task.deadline || task.status === 'COMPLETED') return false;
    return new Date(task.deadline) < new Date();
};
</script>

<template>
    <div class="rounded-md border">
        <Table>
            <TableHeader>
                <TableRow>
                    <TableHead class="w-[300px]">Título</TableHead>
                    <TableHead>Status</TableHead>
                    <TableHead>Prioridade</TableHead>
                    <TableHead>Prazo</TableHead>
                    <TableHead class="text-right">Criada em</TableHead>
                    <TableHead class="w-[100px]"></TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                <TableEmpty v-if="tasks.length === 0" :colspan="6">
                    Nenhuma tarefa encontrada
                </TableEmpty>
                <TableRow v-for="task in tasks" :key="task.id">
                    <TableCell class="font-medium">
                        <div>
                            <p class="font-semibold">{{ task.title }}</p>
                            <p
                                v-if="task.description"
                                class="line-clamp-1 text-xs text-muted-foreground"
                            >
                                {{ task.description }}
                            </p>
                        </div>
                    </TableCell>
                    <TableCell>
                        <Badge :variant="getStatusVariant(task.status)">
                            {{ getStatusLabel(task.status) }}
                        </Badge>
                    </TableCell>
                    <TableCell>
                        <Badge :variant="getPriorityVariant(task.priority)">
                            {{ getPriorityLabel(task.priority) }}
                        </Badge>
                    </TableCell>
                    <TableCell>
                        <span
                            :class="{
                                'text-destructive font-semibold':
                                    isOverdue(task),
                            }"
                        >
                            {{ formatDate(task.deadline) }}
                        </span>
                    </TableCell>
                    <TableCell class="text-right text-sm text-muted-foreground">
                        {{ formatDate(task.created_at) }}
                    </TableCell>
                    <TableCell class="text-right">
                        <div class="flex items-center justify-end gap-1">
                            <Button
                                variant="ghost"
                                size="icon"
                                @click="handleEditClick(task)"
                                class="h-8 w-8 text-muted-foreground hover:text-primary"
                            >
                                <Pencil :size="16" />
                            </Button>
                            <Button
                                variant="ghost"
                                size="icon"
                                @click="handleDeleteClick(task)"
                                class="h-8 w-8 text-muted-foreground hover:text-destructive"
                            >
                                <Trash2 :size="16" />
                            </Button>
                        </div>
                    </TableCell>
                </TableRow>
            </TableBody>
        </Table>
    </div>

    <!--DELETE-->

    <AlertDialog v-model:open="showDeleteDialog">
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle>Excluir Tarefa?</AlertDialogTitle>
                <AlertDialogDescription>
                    Tem certeza que deseja excluir a tarefa "{{
                        taskToDelete?.title
                    }}"? Esta ação não pode ser desfeita.
                </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
                <AlertDialogCancel>Cancelar</AlertDialogCancel>
                <AlertDialogAction
                    @click="handleDelete"
                    class="bg-destructive text-destructive-foreground hover:bg-destructive/90"
                >
                    Excluir
                </AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>

    <!--EDIT-->

    <Sheet v-model:open="isEditTaskSheetOpen">
        <SheetContent v-if="editingTask">
            <SheetHeader>
                <SheetTitle>Editar Tarefa</SheetTitle>
                <SheetDescription>
                    Atualize as informações da tarefa "{{ editingTask.title }}"
                </SheetDescription>
            </SheetHeader>

            <Form
                :action="update(editingTask.id).url"
                method="put"
                class="flex flex-1 flex-col"
                @success="handleEditTaskSuccess"
                v-slot="{ errors, processing }"
            >
                <div class="flex-1 overflow-y-auto">
                    <div class="grid auto-rows-min gap-6 px-4">
                        <div class="space-y-2">
                            <Label for="edit-task-title">Título *</Label>
                            <Input
                                id="edit-task-title"
                                name="title"
                                required
                                placeholder="Ex: Implementar login"
                                :model-value="editingTask.title"
                            />
                            <InputError :message="errors.title" />
                        </div>

                        <div class="space-y-2">
                            <Label for="edit-task-description">Descrição</Label>
                            <Textarea
                                id="edit-task-description"
                                name="description"
                                placeholder="Descrição detalhada da tarefa"
                                rows="4"
                                :model-value="editingTask.description || ''"
                            />
                            <InputError :message="errors.description" />
                        </div>

                        <div class="space-y-2">
                            <Label>Status</Label>
                            <TaskStatusToggle :task="editingTask" @change="emit('refresh')" />
                        </div>

                        <div class="space-y-2">
                            <Label for="edit-task-priority">Prioridade *</Label>
                            <input type="hidden" name="priority" :value="selectedPriority" />
                            <Select v-model="selectedPriority">
                                <SelectTrigger id="edit-task-priority">
                                    <SelectValue placeholder="Selecione a prioridade" />
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
                            <Label for="edit-task-deadline">Prazo</Label>
                            <Input
                                id="edit-task-deadline"
                                name="deadline"
                                type="date"
                                :model-value="editingTask.deadline || ''"
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
                        Salvar Alterações
                    </Button>
                    <Button
                        type="button"
                        variant="outline"
                        class="w-full"
                        @click="handleCloseEditTaskSheet"
                    >
                        Cancelar
                    </Button>
                </SheetFooter>
            </Form>
        </SheetContent>
    </Sheet>
</template>
