<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import EmptyState from '@/components/common/EmptyState.vue';
import Pagination from '@/components/common/Pagination.vue';
import ProjectCard from '@/components/common/ProjectCard.vue';
import ProjectEditDialog from '@/components/common/ProjectEditDialog.vue';
import { Button } from '@/components/ui/button';
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
import { show, store } from '@/routes/projects';
import type { BreadcrumbItem, PaginatedResponse, Project } from '@/types';
import { Form, Head, router } from '@inertiajs/vue3';
import { Plus } from 'lucide-vue-next';
import { ref } from 'vue';

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
const editingProject = ref<Project | null>(null);

const handleCreateProject = () => {
    isCreateSheetOpen.value = true;
    selectedColorCreate.value = '#6366f1';
};

const handleEditProject = (project: Project) => {
    editingProject.value = project;
    isEditDialogOpen.value = true;
};

const handleCreateSuccess = () => {
    isCreateSheetOpen.value = false;
    setTimeout(() => {
        router.reload({
            preserveState: false,
            preserveScroll: true,
        });
    }, 100);
};

const handleEditSuccess = () => {
    router.reload({ only: ['projects'] });
};

const handleCloseCreateSheet = () => {
    isCreateSheetOpen.value = false;
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
                        @show="router.visit(show(project.id))"
                        @edit="handleEditProject"
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
                                <Label for="create-name"
                                    >Nome do Projeto *</Label
                                >
                                <Input
                                    id="create-name"
                                    name="name"
                                    required
                                    placeholder="Ex: Meu Projeto"
                                />
                                <InputError :message="errors.name" />
                            </div>

                            <div class="space-y-2">
                                <Label for="create-description"
                                    >Descrição</Label
                                >
                                <Input
                                    id="create-description"
                                    name="description"
                                    placeholder="Descrição opcional do projeto"
                                />
                                <InputError :message="errors.description" />
                            </div>

                            <div class="space-y-2">
                                <Label for="create-color"
                                    >Cor Identificativa *</Label
                                >
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
                        <Button
                            type="submit"
                            :disabled="processing"
                            class="w-full"
                        >
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

        <ProjectEditDialog
            v-model:open="isEditDialogOpen"
            :project="editingProject"
            @success="handleEditSuccess"
        />
    </AppLayout>
</template>
