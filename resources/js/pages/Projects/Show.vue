<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import ProjectEditDialog from '@/components/common/ProjectEditDialog.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { index } from '@/routes/projects';
import type { BreadcrumbItem, Project } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { ArrowLeft, Edit } from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface Props {
    project: { data: Project };
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
        href: `/projects/${props.project.data.id}`,
    },
];

const headerTitle = 'Tarefa';

const isEditDialogOpen = ref(false);

const handleEdit = () => {
    isEditDialogOpen.value = true;
};

const handleEditSuccess = () => {
    router.reload({ only: ['project'] });
};

const handleBack = () => {
    router.visit(index.url());
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

            <Card class="flex-1">
                <CardHeader>
                    <CardTitle>Informações do Projeto</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div>
                        <p class="text-sm font-medium text-muted-foreground">
                            Nome
                        </p>
                        <p class="text-lg">{{ project.name }}</p>
                    </div>
                    <div v-if="project.description">
                        <p class="text-sm font-medium text-muted-foreground">
                            Descrição
                        </p>
                        <p class="text-lg">{{ project.description }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-muted-foreground">
                            Cor Identificativa
                        </p>
                        <div class="flex items-center gap-2">
                            <div
                                class="h-8 w-8 rounded border"
                                :style="{ backgroundColor: project.color }"
                            />
                            <span class="font-mono text-sm">{{
                                project.color
                            }}</span>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-muted-foreground">
                            Total de Tarefas
                        </p>
                        <p class="text-2xl font-bold">
                            {{ project.tasks_count || 0 }}
                        </p>
                    </div>
                </CardContent>
            </Card>
        </div>

        <ProjectEditDialog
            v-model:open="isEditDialogOpen"
            :project="project"
            @success="handleEditSuccess"
        />
    </AppLayout>
</template>
