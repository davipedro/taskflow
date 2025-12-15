<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { dashboard, login, register } from '@/routes';
import { ArrowRight } from 'lucide-vue-next';
import { Head, Link } from '@inertiajs/vue3';

withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
);
</script>

<template>
    <Head title="TaskFlow - Gestão de Tarefas Colaborativa" />

    <div class="relative flex min-h-screen flex-col overflow-hidden bg-background">
        <div class="absolute inset-0 z-0 bg-background">
            <div
                class="absolute inset-0 bg-gradient-to-br from-background via-background to-muted"
            ></div>
            <div class="absolute inset-0 bg-black/50"></div>
        </div>

        <div class="relative z-10 flex flex-1 flex-col">
            <header class="w-full py-6">
                <div
                    class="container mx-auto flex items-center justify-between px-4 md:px-6"
                >
                    <Link
                        :href="dashboard()"
                        class="text-xl font-bold tracking-tight text-white transition-opacity hover:opacity-90"
                    >
                        TaskFlow
                    </Link>

                    <nav class="flex items-center gap-6">
                        <Link
                            v-if="$page.props.auth.user"
                            :href="dashboard()"
                            class="text-sm font-medium text-gray-200 transition-colors hover:text-white"
                        >
                            Dashboard
                        </Link>
                        <template v-else>
                            <Link
                                :href="login()"
                                class="text-sm font-medium text-gray-200 transition-colors hover:text-white"
                            >
                                Entrar
                            </Link>
                            <Link v-if="canRegister" :href="register()">
                                <Button
                                    class="h-9 rounded-md bg-white px-5 text-sm font-semibold text-black shadow-lg shadow-white/5 transition-all hover:bg-gray-200"
                                >
                                    Começar Agora
                                </Button>
                            </Link>
                        </template>
                    </nav>
                </div>
            </header>

            <main
                class="flex flex-1 flex-col items-center justify-center px-4 text-center md:px-6"
            >
                <div
                    class="mx-auto max-w-4xl space-y-6 animate-in fade-in-0 slide-in-from-bottom-4 duration-700"
                >
                    <h1
                        class="text-4xl font-bold tracking-tight text-white drop-shadow-xl md:text-5xl lg:text-6xl"
                    >
                        Gerencie tarefas com
                        <span
                            class="mt-1 block bg-gradient-to-r from-gray-100 to-gray-400 bg-clip-text text-transparent"
                        >
                            fluidez e precisão.
                        </span>
                    </h1>

                    <p
                        class="mx-auto max-w-2xl text-lg font-medium leading-relaxed text-gray-300 drop-shadow-md md:text-xl"
                    >
                        O sistema operacional para equipes modernas. Planeje
                        sprints, gerencie roadmaps e automatize fluxos de
                        trabalho.
                    </p>

                    <div class="flex justify-center pt-6">
                        <Link :href="canRegister ? register() : login()">
                            <Button
                                size="lg"
                                class="group h-12 gap-2 rounded-full bg-white px-8 text-base font-bold text-black shadow-[0_0_30px_-10px_rgba(255,255,255,0.3)] transition-all hover:scale-105 hover:bg-gray-100"
                            >
                                Começar Agora
                                <ArrowRight
                                    :size="16"
                                    class="transition-transform group-hover:translate-x-1"
                                />
                            </Button>
                        </Link>
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>
