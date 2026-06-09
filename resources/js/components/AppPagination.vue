<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight } from '@lucide/vue';
import { Button } from '@/components/ui/button';

type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

type PaginationMeta = {
    current_page: number;
    last_page: number;
    from: number;
    to: number;
    total: number;
};

type Props = {
    links: PaginationLink[];
    meta: PaginationMeta;
};

defineProps<Props>();
</script>

<template>
    <div
        v-if="meta.last_page > 1"
        class="flex items-center justify-between gap-4"
    >
        <p class="text-muted-foreground text-sm">
            Showing {{ meta.from }} to {{ meta.to }} of {{ meta.total }} results
        </p>

        <div class="flex items-center gap-1">
            <template v-for="(link, idx) in links" :key="idx">
                <Button
                    v-if="link.label.includes('Previous')"
                    variant="outline"
                    size="sm"
                    :disabled="!link.url"
                    as-child
                >
                    <Link :href="link.url || '#'">
                        <ChevronLeft class="h-4 w-4" />
                    </Link>
                </Button>

                <Button
                    v-else-if="link.label.includes('Next')"
                    variant="outline"
                    size="sm"
                    :disabled="!link.url"
                    as-child
                >
                    <Link :href="link.url || '#'">
                        <ChevronRight class="h-4 w-4" />
                    </Link>
                </Button>

                <Button
                    v-else
                    :variant="link.active ? 'default' : 'ghost'"
                    size="sm"
                    :disabled="!link.url"
                    as-child
                >
                    <Link :href="link.url || '#'">
                        {{ link.label }}
                    </Link>
                </Button>
            </template>
        </div>
    </div>
</template>
