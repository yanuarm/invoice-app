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
import { index, edit as editRoute, destroy as destroyRoute, create as createRoute } from '@/routes/customers';

type Customer = {
    id: number;
    code: string;
    name: string;
    email: string | null;
    phone: string | null;
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
    customers: {
        data: Customer[];
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
            { title: 'Customers', href: index() },
        ],
    },
});

function destroy(customerId: number) {
    router.delete(destroyRoute.url(customerId));
}
</script>

<template>
    
    <div class="flex flex-col gap-6 p-6">
    <Head title="Customers" />

    <Heading title="Customers" description="Manage your customers" />

    <div class="mb-4 flex items-center justify-between gap-4">
        <div class="flex items-center gap-2">
            <Input
                class="max-w-xs"
                placeholder="Search customers..."
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
                :model-value="filters.status ?? 'all'"
                @update:model-value="
                    (value: unknown) => {
                        const status = value as string
                        router.get(
                            index.url(),
                            status === 'all'
                                ? {} 
                                : { status },
                            { preserveState: true, replace: true },
                        )
                    }
                "
            >
                <SelectTrigger class="w-[140px]">
                    <SelectValue placeholder="All status" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem value="all">All status</SelectItem>
                    <SelectItem value="active">Active</SelectItem>
                    <SelectItem value="inactive">Inactive</SelectItem>
                </SelectContent>
            </Select>
        </div>
        <Link :href="createRoute()">
            <Button>
                <Plus class="mr-1 h-4 w-4" />
                Create Customer
            </Button>
        </Link>
    </div>

    <AppTable
        :columns="[
            { key: 'code', label: 'Code' },
            { key: 'name', label: 'Name' },
            { key: 'email', label: 'Email' },
            { key: 'phone', label: 'Phone' },
            { key: 'status', label: 'Status' },
        ]"
        :data="customers.data"
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
                    description="This will permanently delete this customer."
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

    <div v-if="customers?.meta?.total > 0" class="mt-4">
        <AppPagination :links="customers.links" :meta="customers.meta" />
    </div>
    </div>
</template>
