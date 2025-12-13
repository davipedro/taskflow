<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { store } from '@/routes/projects';
import type { BreadcrumbItem } from '@/types';
import { Form, Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { toast } from 'vue-sonner';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Meus Projetos',
        href: '/projects',
    },
    {
        title: 'Criar Projeto',
        href: '/projects/create',
    },
];

const selectedColor = ref('#6366f1');
</script>

<template>
    <Head title="Criar Projeto" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="flex items-center justify-between">
                <Heading
                    title="Criar Novo Projeto"
                    description="Preencha as informações do seu projeto"
                />
            </div>

            <Card class="flex flex-1 flex-col p-6">
                <Form
                    v-bind="store.form()"
                    class="space-y-6"
                    @success="
                        () => toast.success('Projeto criado com sucesso!')
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
                        />
                        <InputError :message="errors.name" />
                    </div>

                    <div class="space-y-2">
                        <Label for="description">Descrição</Label>
                        <Input
                            id="description"
                            name="description"
                            placeholder="Descrição opcional do projeto"
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
                            Criar Projeto
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
            </Card>
        </div>
    </AppLayout>
</template>
