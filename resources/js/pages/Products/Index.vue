<script setup lang="ts">
import { Head, router, Link } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2 } from '@lucide/vue';
import AppConfirmDialog from '@/components/AppConfirmDialog.vue';
import AppPagination from '@/components/AppPagination.vue';
import AppTable from '@/components/AppTable.vue';
import Heading from '@/components/Heading.vue';

import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { dashboard } from '@/routes';
import { index, edit as editRoute, destroy as destroyRoute, create as createRoute } from '@/routes/products';

type Product = {
    id: number;
    sku: string;
    name: string;
    unit: string;
    price: string;
    status: string;
    created_at: string;
};

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
    products: {
        data: Product[];
        links: PaginationLink[];
        meta: PaginationMeta;
    };
    filters: {
        search?: string;
        status?: string;
        sort?: string;
        direction?: string;
    };
};

defineProps<Props>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard() },
            { title: 'Products', href: index() },
        ],
    },
});

function destroy(productId: number) {
    router.delete(destroyRoute.url(productId));
}
</script>

<template>
    <div class="flex flex-col gap-6 p-6">
    <Head title="Products" />

    <Heading title="Products" description="Manage your products" />

    <div class="mb-4 flex items-center justify-between gap-4">
        <div class="flex items-center gap-2">
            <Input
                class="max-w-xs"
                placeholder="Search products..."
                :model-value="filters.search"
                @input="
                    router.get(
                        index.url(),
                        { search: ($event.target as HTMLInputElement).value },
                        { preserveState: true, replace: true },
                    )
                "
            />
            <Select
                :model-value="filters.status ?? ''"
                @update:model-value="
                    (value: unknown) =>
                        router.get(
                            index.url(),
                            { status: value as string },
                            { preserveState: true, replace: true },
                        )
                "
            >
                <SelectTrigger class="w-[140px]">
                    <SelectValue placeholder="All status" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem value="">All status</SelectItem>
                    <SelectItem value="active">Active</SelectItem>
                    <SelectItem value="inactive">Inactive</SelectItem>
                </SelectContent>
            </Select>
        </div>
        <Link :href="createRoute()">
            <Button>
                <Plus class="mr-1 h-4 w-4" />
                Create Product
            </Button>
        </Link>
    </div>

    <AppTable
        :columns="[
            { key: 'sku', label: 'SKU' },
            { key: 'name', label: 'Name' },
            { key: 'unit', label: 'Unit' },
            { key: 'price', label: 'Price' },
            { key: 'status', label: 'Status' },
        ]"
        :data="products.data"
    >
        <template #actions="{ row }">
            <div class="flex items-center gap-1">
                <Button variant="ghost" size="icon-sm" as-child>
                    <Link :href="editRoute(Number(row.id))">
                        <Pencil class="h-4 w-4" />
                    </Link>
                </Button>
                <AppConfirmDialog
                    :title="'Delete ' + (row.name ?? '') + '?'"
                    description="This will permanently delete this product."
                    @confirm="destroy(Number(row.id))"
                >
                    <template #trigger>
                        <Button variant="ghost" size="icon-sm">
                            <Trash2 class="h-4 w-4" />
                        </Button>
                    </template>
                </AppConfirmDialog>
            </div>
        </template>
    </AppTable>

    <div v-if="products?.meta?.total > 0" class="mt-4">
        <AppPagination :links="products.links" :meta="products.meta" />
    </div>
    </div>
</template>
