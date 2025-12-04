<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import EmptyState from '@/components/common/EmptyState.vue';
import Pagination from '@/components/common/Pagination.vue';
import ProjectCard from '@/components/common/ProjectCard.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, PaginatedResponse, Project } from '@/types';
import { create, edit } from '@/routes/projects';
import { Head, router } from '@inertiajs/vue3';
import { Plus } from 'lucide-vue-next';

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

const handleCreateProject = () => {
    router.visit(create.url());
};

const handleEditProject = (project: Project) => {
    router.visit(edit.url(project.id));
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
    </AppLayout>
</template>
