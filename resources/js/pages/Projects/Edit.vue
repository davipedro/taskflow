<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { update } from '@/routes/projects';
import type { BreadcrumbItem, Project } from '@/types';
import { Form, Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { toast } from 'vue-sonner';

interface Props {
    project: Project;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Meus Projetos',
        href: '/projects',
    },
    {
        title: props.project.name,
        href: `/projects/${props.project.id}`,
    },
    {
        title: 'Editar',
        href: `/projects/${props.project.id}/edit`,
    },
];

const selectedColor = ref(props.project.color || '#6366f1');
</script>

<template>
    <Head :title="`Editar ${props.project.name}`" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-2xl space-y-6">
            <Heading title="Editar Projeto" />

            <Card>
                <CardHeader>
                    <p class="text-sm text-muted-foreground">
                        Atualize as informações do seu projeto
                    </p>
                </CardHeader>
                <CardContent>
                    <Form
                        v-bind="update.form(props.project.data)"
                        class="space-y-6"
                        @success="
                            () =>
                                toast.success('Projeto atualizado com sucesso!')
                        "
                        v-slot="{ errors, processing }"
                    >
                        <div class="space-y-2">
                            <Label for="name">Nome do Projeto *</Label>
                            <Input
                                id="name"
                                name="name"
                                required
                                placeholder="Ex: Meu Projeto"
                                :default-value="props.project.name"
                            />
                            <InputError :message="errors.name" />
                        </div>

                        <div class="space-y-2">
                            <Label for="description">Descrição</Label>
                            <Input
                                id="description"
                                name="description"
                                placeholder="Descrição opcional do projeto"
                                :default-value="props.project.description"
                            />
                            <InputError :message="errors.description" />
                        </div>

                        <div class="space-y-2">
                            <Label for="color">Cor Identificativa *</Label>
                            <div class="flex gap-2">
                                <Input
                                    id="color"
                                    name="color"
                                    type="color"
                                    class="h-10 w-20 cursor-pointer"
                                    v-model="selectedColor"
                                />
                                <Input
                                    type="text"
                                    placeholder="#6366f1"
                                    class="flex-1 font-mono"
                                    v-model="selectedColor"
                                    readonly
                                />
                            </div>
                            <InputError :message="errors.color" />
                        </div>

                        <div class="flex gap-3">
                            <Button type="submit" :disabled="processing">
                                Salvar Alterações
                            </Button>
                            <Button
                                type="button"
                                variant="outline"
                                @click="router.visit('/projects')"
                            >
                                Cancelar
                            </Button>
                        </div>
                    </Form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
