<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

interface Props {
    title: string;
}

defineProps<Props>();
</script>

<template>
    <Head :title="title" />
    <div class="min-h-screen bg-background">
        <slot />
    </div>
</template>
