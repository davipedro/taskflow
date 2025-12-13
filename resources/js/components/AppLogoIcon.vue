<script setup lang="ts">
import { SquareCheckBig } from 'lucide-vue-next';
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <SquareCheckBig class="size-5 text-white dark:text-black" />
</template>
