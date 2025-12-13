<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    Pagination,
    PaginationContent,
    PaginationEllipsis,
    PaginationItem,
    PaginationNext,
    PaginationPrevious,
} from '@/components/ui/pagination';
import type { PaginatedResponse } from '@/types';
import { router } from '@inertiajs/vue3';

interface Props {
    resource: PaginatedResponse;
}

const props = defineProps<Props>();
</script>

<template>
    <Pagination
        :items-per-page="
            props.resource.meta?.per_page || props.resource.per_page
        "
        :total="props.resource.meta?.total || props.resource.total"
        :sibling-count="1"
        show-edges
        :default-page="
            props.resource.meta?.current_page || props.resource.current_page
        "
        class="mx-auto"
    >
        <PaginationContent v-slot="{ items }" class="flex items-center gap-1">
            <div
                v-if="
                    (props.resource.meta?.last_page ||
                        props.resource.last_page) === 1
                "
            >
                <div class="mt-4 text-center text-muted-foreground">
                    Mostrando todos os resultados.
                </div>
            </div>
            <div
                v-if="
                    (props.resource.meta?.last_page ||
                        props.resource.last_page) !== 1
                "
                class="flex items-center gap-1"
            >
                <PaginationPrevious
                    v-if="
                        props.resource.links?.prev ||
                        props.resource.prev_page_url
                    "
                    @click="
                        () =>
                            router.visit(
                                props.resource.links?.prev ||
                                    props.resource.prev_page_url,
                            )
                    "
                />
                <div
                    v-else
                    class="inline-flex h-10 items-center justify-center gap-1 rounded-md px-2.5 text-sm font-medium whitespace-nowrap opacity-0 sm:pr-2.5"
                >
                    <span class="hidden sm:block">Anterior</span>
                </div>

                <template v-for="(item, index) in items" :key="index">
                    <PaginationItem
                        v-if="item.type === 'page'"
                        :value="item.value"
                        as-child
                    >
                        <Button
                            class="h-10 w-10 p-0"
                            :variant="
                                item.value ===
                                (props.resource.meta?.current_page ||
                                    props.resource.current_page)
                                    ? 'default'
                                    : 'outline'
                            "
                            @click="
                                () => {
                                    const link =
                                        props.resource.meta?.links?.[index + 1]
                                            ?.url ||
                                        props.resource.links?.[index + 1]?.url;
                                    if (link) router.visit(link);
                                }
                            "
                        >
                            {{ item.value }}
                        </Button>
                    </PaginationItem>
                    <PaginationEllipsis v-else :index="index" />
                </template>

                <PaginationNext
                    v-if="
                        props.resource.links?.next ||
                        props.resource.next_page_url
                    "
                    @click="
                        () =>
                            router.visit(
                                props.resource.links?.next ||
                                    props.resource.next_page_url,
                            )
                    "
                />
                <div
                    v-else
                    class="inline-flex h-10 items-center justify-center gap-1 rounded-md px-2.5 text-sm font-medium whitespace-nowrap opacity-0 sm:pr-2.5"
                >
                    <span class="hidden sm:block">Próximo</span>
                </div>
            </div>
        </PaginationContent>
    </Pagination>
</template>
