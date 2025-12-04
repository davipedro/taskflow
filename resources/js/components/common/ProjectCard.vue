<script setup lang="ts">
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
import {
    Card,
    CardContent,
    CardFooter,
    CardHeader,
} from '@/components/ui/card';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import type { Project } from '@/types';
import { router, usePage } from '@inertiajs/vue3';
import { CheckSquare, MoreHorizontal } from 'lucide-vue-next';
import { ref } from 'vue';
import { toast } from 'vue-sonner';

interface Props {
    project: Project;
    useEvents?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    useEvents: false,
});

const emit = defineEmits<{
    edit: [project: Project];
    delete: [project: Project];
}>();

const page = usePage();
const isOwner = page.props.auth.user.id === props.project.user_id;

const showDeleteDialog = ref(false);

const handleEdit = () => {
    if (props.useEvents) {
        emit('edit', props.project);
    } else {
        router.visit(`/projects/${props.project.id}/edit`);
    }
};

const handleDelete = () => {
    router.delete(`/projects/${props.project.id}`, {
        onSuccess: () => {
            toast.success('Projeto excluído com sucesso!');
            showDeleteDialog.value = false;
        },
    });
};

const handleCardClick = () => {
    router.visit(`/projects/${props.project.id}`);
};
</script>

<template>
    <Card
        class="group cursor-pointer border-l-4 transition-all hover:border-l-8 hover:shadow-lg"
        :style="{ borderColor: project.color || '#6366f1' }"
        @click="handleCardClick"
    >
        <CardHeader class="flex flex-row items-start justify-between space-y-0 pb-2">
            <h3 class="text-lg font-bold transition-colors group-hover:text-primary">
                {{ project.name }}
            </h3>
            <DropdownMenu v-if="isOwner" @click.stop>
                <DropdownMenuTrigger as-child>
                    <Button variant="ghost" size="icon" class="h-8 w-8">
                        <MoreHorizontal :size="18" />
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end">
                    <DropdownMenuItem @click.stop="handleEdit">
                        Editar
                    </DropdownMenuItem>
                    <DropdownMenuItem
                        @click.stop="showDeleteDialog = true"
                        class="text-destructive"
                    >
                        Excluir
                    </DropdownMenuItem>
                </DropdownMenuContent>
            </DropdownMenu>
        </CardHeader>

        <CardContent class="pb-2">
            <p
                v-if="project.description"
                class="line-clamp-2 text-sm text-muted-foreground"
            >
                {{ project.description }}
            </p>
            <div v-else class="h-10"></div>
        </CardContent>

        <CardFooter class="flex items-center justify-between border-t pt-4">
            <div class="flex items-center gap-2 text-xs text-muted-foreground">
                <CheckSquare :size="14" />
                <span>{{ project.tasks_count }} tarefa(s)</span>
            </div>
        </CardFooter>
    </Card>

    <!-- Alert Dialog de Confirmação de Exclusão -->
    <AlertDialog v-model:open="showDeleteDialog">
        <AlertDialogContent @click.stop>
            <AlertDialogHeader>
                <AlertDialogTitle>Excluir Projeto?</AlertDialogTitle>
                <AlertDialogDescription>
                    Tem certeza que deseja excluir o projeto "{{
                        project.name
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
</template>
