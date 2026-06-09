<script setup lang="ts">
import { LoaderCircle } from '@lucide/vue';
import { cn } from '@/lib/utils';

export type Column = {
    key: string;
    label: string;
    sortable?: boolean;
};

type Props = {
    columns: Column[];
    data: Record<string, unknown>[];
    loading?: boolean;
    class?: string;
};

defineProps<Props>();
</script>

<template>
    <div :class="cn('relative overflow-x-auto rounded-md border', $props.class)">
        <table class="w-full text-left text-sm">
            <thead class="bg-muted/50 text-muted-foreground border-b text-xs uppercase">
                <tr>
                    <th
                        v-for="col in columns"
                        :key="col.key"
                        class="px-4 py-3 font-medium"
                    >
                        {{ col.label }}
                    </th>
                    <th v-if="$slots.actions" class="px-4 py-3 font-medium">
                        <span class="sr-only">Actions</span>
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y">
                <tr
                    v-for="(row, rowIdx) in data"
                    :key="rowIdx"
                    class="hover:bg-muted/50 transition-colors"
                >
                    <td
                        v-for="col in columns"
                        :key="col.key"
                        class="px-4 py-3"
                    >
                        {{ row[col.key] }}
                    </td>
                    <td v-if="$slots.actions" class="px-4 py-3">
                        <slot name="actions" :row="row" :index="rowIdx" />
                    </td>
                </tr>
                <tr v-if="!loading && data.length === 0">
                    <td
                        :colspan="columns.length + ($slots.actions ? 1 : 0)"
                        class="text-muted-foreground px-4 py-8 text-center"
                    >
                        No data found.
                    </td>
                </tr>
            </tbody>
        </table>
        <div
            v-if="loading"
            class="bg-background/60 absolute inset-0 flex items-center justify-center"
        >
            <LoaderCircle class="h-5 w-5 animate-spin" />
        </div>
    </div>
</template>
