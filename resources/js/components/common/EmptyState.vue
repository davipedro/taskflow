<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { FolderKanban } from 'lucide-vue-next';

interface Props {
    title?: string;
    description?: string;
    buttonText?: string;
    buttonAction?: () => void;
}

withDefaults(defineProps<Props>(), {
    title: 'Nenhum item encontrado',
    description: 'Comece criando seu primeiro item.',
    buttonText: 'Criar',
});
</script>

<template>
    <div
        class="flex flex-col items-center justify-center rounded-lg border border-dashed p-12 text-center"
    >
        <div class="mb-4 rounded-full bg-muted p-4">
            <FolderKanban :size="32" class="text-muted-foreground" />
        </div>
        <h3 class="mb-2 text-lg font-semibold">{{ title }}</h3>
        <p class="mb-6 text-sm text-muted-foreground">{{ description }}</p>
        <Button v-if="buttonAction" @click="buttonAction">
            {{ buttonText }}
        </Button>
    </div>
</template>
