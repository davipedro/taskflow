<script setup lang="ts">
import InputError from '@/components/InputError.vue';
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
import { update } from '@/routes/projects';
import type { Project } from '@/types';
import { Form } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { toast } from 'vue-sonner';

interface Props {
    project: Project | null;
    open: boolean;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    'update:open': [value: boolean];
    success: [];
}>();

const selectedColor = ref('#6366f1');

watch(
    () => props.project,
    (newProject) => {
        if (newProject) {
            selectedColor.value = newProject.color || '#6366f1';
        }
    },
    { immediate: true },
);

const handleSuccess = () => {
    emit('update:open', false);
    emit('success');
    toast.success('Projeto atualizado com sucesso!');
};

const handleError = () => {
    toast.error('Ocorreu um erro ao atualizar o projeto.');
};

const handleClose = () => {
    emit('update:open', false);
};
</script>

<template>
    <Dialog :open="open" @update:open="emit('update:open', $event)">
        <DialogContent v-if="project" class="sm:max-w-[500px]">
            <DialogHeader>
                <DialogTitle>Editar Projeto</DialogTitle>
                <DialogDescription>
                    Atualize as informações do seu projeto
                </DialogDescription>
            </DialogHeader>

            <Form
                v-bind="update.form(project.id)"
                class="space-y-6"
                @success="handleSuccess"
                @error="handleError"
                v-slot="{ errors, processing }"
            >
                <div class="space-y-2">
                    <Label for="edit-name">Nome do Projeto *</Label>
                    <Input
                        id="edit-name"
                        name="name"
                        required
                        placeholder="Ex: Meu Projeto"
                        :default-value="project.name"
                    />
                    <InputError :message="errors.name" />
                </div>

                <div class="space-y-2">
                    <Label for="edit-description">Descrição</Label>
                    <Input
                        id="edit-description"
                        name="description"
                        placeholder="Descrição opcional do projeto"
                        :default-value="project.description"
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

                <DialogFooter>
                    <Button
                        type="button"
                        variant="outline"
                        @click="handleClose"
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
</template>
