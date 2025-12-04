<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import EmptyState from '@/components/common/EmptyState.vue';
import Pagination from '@/components/common/Pagination.vue';
import ProjectCard from '@/components/common/ProjectCard.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Sheet,
    SheetContent,
    SheetDescription,
    SheetFooter,
    SheetHeader,
    SheetTitle,
} from '@/components/ui/sheet';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, PaginatedResponse, Project } from '@/types';
import { show, store, update } from '@/routes/projects';
import { Form, Head, router } from '@inertiajs/vue3';
import { ExternalLink, Plus } from 'lucide-vue-next';
import { ref } from 'vue';
import { toast } from 'vue-sonner';

interface Props {
    projects: PaginatedResponse<Project>;
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Meus Projetos',
        href: '/projects',
    },
];

const isCreateSheetOpen = ref(false);
const selectedColorCreate = ref('#6366f1');

const isEditDialogOpen = ref(false);
const isShowingProject = ref(false);
const selectedColorEdit = ref('#6366f1');
const editingProject = ref<Project | null>(null);
const showingProject = ref<Project | null>(null);

const handleCreateProject = () => {
    isCreateSheetOpen.value = true;
    selectedColorCreate.value = '#6366f1';
};

const handleEditProject = (project: Project) => {
    editingProject.value = project;
    selectedColorEdit.value = project.color || '#6366f1';
    isEditDialogOpen.value = true;
};

const handleShowProject = (project: Project) => {
    showingProject.value = project;
    isShowingProject.value = true;
};

const handleCreateSuccess = () => {
    isCreateSheetOpen.value = false;
    toast.success('Projeto criado com sucesso!');
    setTimeout(() => {
        router.visit('/projects', {
            preserveState: false,
            preserveScroll: true,
        });
    }, 100);
};

const handleEditSuccess = () => {
    isEditDialogOpen.value = false;
    toast.success('Projeto atualizado com sucesso!');
    router.reload({ only: ['projects'] });
};

const handleCloseCreateSheet = () => {
    isCreateSheetOpen.value = false;
};

const handleCloseEditDialog = () => {
    isEditDialogOpen.value = false;
    editingProject.value = null;
};

const handleCloseShowDialog = () => {
    isShowingProject.value = false;
    showingProject.value = null;
};

const handleOpenFullDetails = (project: Project) => {
    window.open(show.url(project.id), '_blank');
};
</script>

<template>
    <Head title="Meus Projetos" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="flex items-center justify-between">
                <div>
                    <Heading
                        title="Meus Projetos"
                        description="Gerencie seus projetos e tarefas"
                    />
                </div>
                <Button @click="handleCreateProject">
                    <Plus :size="16" />
                    Novo Projeto
                </Button>
            </div>

            <div v-if="projects.data.length > 0">
                <div
                    class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3"
                >
                    <ProjectCard
                        v-for="project in projects.data"
                        :key="project.id"
                        :project="project"
                        :use-events="true"
                        @edit="handleEditProject"
                        @show="handleShowProject"
                    />
                </div>

                <div class="mt-8">
                    <Pagination :resource="projects" />
                </div>
            </div>

            <EmptyState
                v-else
                title="Nenhum projeto encontrado"
                description="Comece criando seu primeiro projeto para organizar suas tarefas."
                button-text="Criar Primeiro Projeto"
                :button-action="handleCreateProject"
            />
        </div>

        <Sheet v-model:open="isCreateSheetOpen">
            <SheetContent>
                <SheetHeader>
                    <SheetTitle>Criar Novo Projeto</SheetTitle>
                    <SheetDescription>
                        Preencha as informações do seu projeto
                    </SheetDescription>
                </SheetHeader>

                <Form
                    v-bind="store.form()"
                    class="flex flex-1 flex-col"
                    @success="handleCreateSuccess"
                    v-slot="{ errors, processing }"
                >
                    <div class="flex-1 overflow-y-auto">
                        <div class="grid auto-rows-min gap-6 px-4">
                            <div class="space-y-2">
                                <Label for="create-name">Nome do Projeto *</Label>
                                <Input
                                    id="create-name"
                                    name="name"
                                    required
                                    placeholder="Ex: Meu Projeto"
                                />
                                <InputError :message="errors.name" />
                            </div>

                            <div class="space-y-2">
                                <Label for="create-description">Descrição</Label>
                                <Input
                                    id="create-description"
                                    name="description"
                                    placeholder="Descrição opcional do projeto"
                                />
                                <InputError :message="errors.description" />
                            </div>

                            <div class="space-y-2">
                                <Label for="create-color">Cor Identificativa *</Label>
                                <div class="flex gap-2">
                                    <Input
                                        id="create-color"
                                        name="color"
                                        type="color"
                                        class="h-10 w-20 cursor-pointer"
                                        v-model="selectedColorCreate"
                                    />
                                    <Input
                                        type="text"
                                        placeholder="#6366f1"
                                        class="flex-1 font-mono"
                                        v-model="selectedColorCreate"
                                        readonly
                                    />
                                </div>
                                <InputError :message="errors.color" />
                            </div>
                        </div>
                    </div>

                    <SheetFooter class="flex-col gap-2">
                        <Button type="submit" :disabled="processing" class="w-full">
                            Criar Projeto
                        </Button>
                        <Button
                            type="button"
                            variant="outline"
                            class="w-full"
                            @click="handleCloseCreateSheet"
                        >
                            Cancelar
                        </Button>
                    </SheetFooter>
                </Form>
            </SheetContent>
        </Sheet>

        <Dialog v-model:open="isShowingProject">
            <DialogContent v-if="showingProject" class="sm:max-w-[600px]">
                <DialogHeader>
                    <DialogTitle>Informações do Projeto</DialogTitle>
                    <DialogDescription>
                        Detalhes completos do projeto
                    </DialogDescription>
                </DialogHeader>

                <div class="space-y-4">
                    <div>
                        <p class="text-sm font-medium text-muted-foreground">
                            Nome
                        </p>
                        <p class="text-lg">{{ showingProject.name }}</p>
                    </div>
                    <div v-if="showingProject.description">
                        <p class="text-sm font-medium text-muted-foreground">
                            Descrição
                        </p>
                        <p class="text-lg">{{ showingProject.description }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-muted-foreground">
                            Cor Identificativa
                        </p>
                        <div class="flex items-center gap-2">
                            <div
                                class="h-8 w-8 rounded border"
                                :style="{ backgroundColor: showingProject.color }"
                            />
                            <span class="font-mono text-sm">{{
                                showingProject.color
                            }}</span>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-muted-foreground">
                            Total de Tarefas
                        </p>
                        <p class="text-2xl font-bold">
                            {{ showingProject.tasks_count || 0 }}
                        </p>
                    </div>
                </div>

                <DialogFooter>
                    <Button variant="outline" @click="handleCloseShowDialog">
                        Fechar
                    </Button>
                    <Button @click="handleOpenFullDetails(showingProject)">
                        <ExternalLink :size="16" />
                        Ver Detalhes Completos
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <Dialog v-model:open="isEditDialogOpen">
            <DialogContent v-if="editingProject" class="sm:max-w-[500px]">
                <DialogHeader>
                    <DialogTitle>Editar Projeto</DialogTitle>
                    <DialogDescription>
                        Atualize as informações do seu projeto
                    </DialogDescription>
                </DialogHeader>

                <Form
                    v-bind="update.form(editingProject)"
                    class="space-y-6"
                    @success="handleEditSuccess"
                    v-slot="{ errors, processing }"
                >
                    <div class="space-y-2">
                        <Label for="edit-name">Nome do Projeto *</Label>
                        <Input
                            id="edit-name"
                            name="name"
                            required
                            placeholder="Ex: Meu Projeto"
                            :default-value="editingProject.name"
                        />
                        <InputError :message="errors.name" />
                    </div>

                    <div class="space-y-2">
                        <Label for="edit-description">Descrição</Label>
                        <Input
                            id="edit-description"
                            name="description"
                            placeholder="Descrição opcional do projeto"
                            :default-value="editingProject.description"
                        />
                        <InputError :message="errors.description" />
                    </div>

                    <div class="space-y-2">
                        <Label for="edit-color">Cor Identificativa *</Label>
                        <div class="flex gap-2">
                            <Input
                                id="edit-color"
                                name="color"
                                type="color"
                                class="h-10 w-20 cursor-pointer"
                                v-model="selectedColorEdit"
                            />
                            <Input
                                type="text"
                                placeholder="#6366f1"
                                class="flex-1 font-mono"
                                v-model="selectedColorEdit"
                                readonly
                            />
                        </div>
                        <InputError :message="errors.color" />
                    </div>

                    <DialogFooter>
                        <Button
                            type="button"
                            variant="outline"
                            @click="handleCloseEditDialog"
                        >
                            Cancelar
                        </Button>
                        <Button type="submit" :disabled="processing">
                            Salvar Alterações
                        </Button>
                    </DialogFooter>
                </Form>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
