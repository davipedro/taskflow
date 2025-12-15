<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TaskPriorityToggle from '@/components/common/TaskPriorityToggle.vue';
import TaskStatusToggle from '@/components/common/TaskStatusToggle.vue';
import { useDateFormatter } from '@/composables/useDateFormatter';
import { useTaskPriority } from '@/composables/useTaskPriority';
import { useTaskStatus } from '@/composables/useTaskStatus';
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
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
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
import {
    Table,
    TableBody,
    TableCell,
    TableEmpty,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { Textarea } from '@/components/ui/textarea';
import { destroy as destroyTask, update } from '@/routes/tasks';
import type { Task } from '@/types';
import { Form, router } from '@inertiajs/vue3';
import { Pencil, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';
import { toast } from 'vue-sonner';

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

const showDetailsDialog = ref(false);
const taskDetails = ref<Task | null>(null);

const handleRowClick = (task: Task) => {
    taskDetails.value = task;
    showDetailsDialog.value = true;
};

const handleEditClick = (task: Task, event: Event) => {
    event.stopPropagation();
    editingTask.value = task;
    selectedPriority.value = task.priority;
    isEditTaskSheetOpen.value = true;
};

const handleDeleteClick = (task: Task, event: Event) => {
    event.stopPropagation();
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

const { getStatusLabel, getStatusVariant } = useTaskStatus();
const { getPriorityLabel, getPriorityVariant } = useTaskPriority();
const { formatDate, formatDateForInput, isOverdue } = useDateFormatter();
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
                <TableRow
                    v-for="task in tasks"
                    :key="task.id"
                    class="cursor-pointer hover:bg-muted/50"
                    @click="handleRowClick(task)"
                >
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
                        <div class="flex items-center gap-2" @click.stop>
                            <Badge :variant="getStatusVariant(task.status)">
                                {{ getStatusLabel(task.status) }}
                            </Badge>
                            <TaskStatusToggle
                                :task="task"
                                @change="emit('refresh')"
                            />
                        </div>
                    </TableCell>
                    <TableCell>
                        <div @click.stop>
                            <TaskPriorityToggle
                                :task="task"
                                @change="emit('refresh')"
                            />
                        </div>
                    </TableCell>
                    <TableCell>
                        <span
                            :class="{
                                'font-semibold text-destructive':
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
                                @click="(e) => handleEditClick(task, e)"
                                class="h-8 w-8 text-muted-foreground hover:text-primary"
                            >
                                <Pencil :size="16" />
                            </Button>
                            <Button
                                variant="ghost"
                                size="icon"
                                @click="(e) => handleDeleteClick(task, e)"
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

    <!--DETAILS DIALOG-->

    <Dialog v-model:open="showDetailsDialog">
        <DialogContent v-if="taskDetails" class="max-w-2xl">
            <DialogHeader>
                <DialogTitle class="text-2xl">{{
                    taskDetails.title
                }}</DialogTitle>
                <DialogDescription v-if="taskDetails.description">
                    {{ taskDetails.description }}
                </DialogDescription>
            </DialogHeader>

            <div class="grid gap-6 py-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <h4 class="text-sm font-medium text-muted-foreground">
                            Status
                        </h4>
                        <Badge :variant="getStatusVariant(taskDetails.status)">
                            {{ getStatusLabel(taskDetails.status) }}
                        </Badge>
                    </div>

                    <div class="space-y-2">
                        <h4 class="text-sm font-medium text-muted-foreground">
                            Prioridade
                        </h4>
                        <Badge
                            :variant="getPriorityVariant(taskDetails.priority)"
                        >
                            {{ getPriorityLabel(taskDetails.priority) }}
                        </Badge>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <h4 class="text-sm font-medium text-muted-foreground">
                            Prazo
                        </h4>
                        <p
                            class="text-sm"
                            :class="{
                                'font-semibold text-destructive':
                                    isOverdue(taskDetails),
                            }"
                        >
                            {{ formatDate(taskDetails.deadline) }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <h4 class="text-sm font-medium text-muted-foreground">
                            {{
                                taskDetails.status === 'COMPLETED'
                                    ? 'Concluída em'
                                    : 'Criada em'
                            }}
                        </h4>
                        <p class="text-sm">
                            {{
                                taskDetails.status === 'COMPLETED' &&
                                taskDetails.completed_at
                                    ? formatDate(taskDetails.completed_at)
                                    : formatDate(taskDetails.created_at)
                            }}
                        </p>
                    </div>
                </div>

                <div v-if="taskDetails.project" class="space-y-2">
                    <h4 class="text-sm font-medium text-muted-foreground">
                        Projeto
                    </h4>
                    <div class="flex items-center gap-2">
                        <div
                            class="h-3 w-3 rounded-full"
                            :style="{
                                backgroundColor: taskDetails.project.color,
                           }"
                        />
                        <p class="text-sm">{{ taskDetails.project.name }}</p>
                    </div>
                </div>
            </div>
        </DialogContent>
    </Dialog>

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
                            <div
                                class="flex h-10 items-center rounded-md border border-input bg-muted px-3"
                            >
                                <Badge
                                    :variant="getStatusVariant(editingTask.status)"
                                >
                                    {{ getStatusLabel(editingTask.status) }}
                                </Badge>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="edit-task-priority">Prioridade *</Label>
                            <input
                                type="hidden"
                                name="priority"
                                :value="selectedPriority"
                            />
                            <Select v-model="selectedPriority">
                                <SelectTrigger id="edit-task-priority">
                                    <SelectValue
                                        placeholder="Selecione a prioridade"
                                    />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="LOW">Baixa</SelectItem>
                                    <SelectItem value="MEDIUM"
                                        >Média</SelectItem
                                    >
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
                                :model-value="formatDateForInput(editingTask.deadline)"
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
