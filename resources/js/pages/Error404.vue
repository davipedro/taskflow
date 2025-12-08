<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import ErrorLayout from '@/layouts/ErrorLayout.vue';
import { router } from '@inertiajs/vue3';
import { FileQuestion, Home, ArrowLeft } from 'lucide-vue-next';

const goBack = () => {
    if (window.history.length > 1) {
        window.history.back();
    } else {
        router.visit('/projects');
    }
};

const goHome = () => {
    router.visit('/projects');
};
</script>

<template>
    <ErrorLayout title="Página Não Encontrada">
        <div
            class="flex min-h-screen flex-col items-center justify-center p-4"
        >
            <Card class="w-full max-w-md p-8">
                <div class="flex flex-col items-center space-y-6 text-center">
                    <div
                        class="flex h-20 w-20 items-center justify-center rounded-full bg-muted"
                    >
                        <FileQuestion class="h-10 w-10 text-muted-foreground" />
                    </div>

                    <div class="space-y-2">
                        <h1 class="text-4xl font-bold">404</h1>
                        <h2 class="text-xl font-semibold">
                            Página Não Encontrada
                        </h2>
                        <p class="text-sm text-muted-foreground">
                            A página que você está procurando não existe ou foi
                            movida.
                        </p>
                    </div>

                    <div class="flex w-full flex-col gap-3">
                        <Button @click="goBack" variant="default" class="w-full">
                            <ArrowLeft :size="16" />
                            Voltar
                        </Button>
                        <Button @click="goHome" variant="outline" class="w-full">
                            <Home :size="16" />
                            Ir para Projetos
                        </Button>
                    </div>
                </div>
            </Card>
        </div>
    </ErrorLayout>
</template>
