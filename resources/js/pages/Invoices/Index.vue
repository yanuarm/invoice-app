<script setup lang="ts">
import { Head, router, Link } from '@inertiajs/vue3';
import { Eye, Pencil, Plus, Trash2 } from '@lucide/vue';
import AppConfirmDialog from '@/components/AppConfirmDialog.vue';
import AppPagination from '@/components/AppPagination.vue';
import AppTable from '@/components/AppTable.vue';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
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
import { index, show as showRoute, edit as editRoute, destroy as destroyRoute, create as createRoute } from '@/routes/invoices';

type Invoice = {
    id: number;
    invoice_number: string;
    customer: { id: number; name: string } | null;
    invoice_date: string;
    due_date: string;
    grand_total: string;
    status: string;
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
    invoices: {
        data: Invoice[];
        links: PaginationLink[];
        meta: PaginationMeta;
    };
    filters: {
        search?: string;
        status?: string;
        sort?: string;
        direction?: string;
        date_from?: string;
        date_to?: string;
    };
};

defineProps<Props>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard() },
            { title: 'Invoices', href: index() },
        ],
    },
});

const statusColors: Record<string, string> = {
    draft: 'secondary',
    sent: 'default',
    partial: 'warning',
    paid: 'success',
    overdue: 'destructive',
    cancelled: 'outline',
};

function destroy(invoiceId: number) {
    router.delete(destroyRoute.url(invoiceId));
}

function getStatusVariant(status: string): string {
    return statusColors[status] ?? 'secondary';
}
</script>

<template>
    <div class="flex flex-col gap-6 p-6">
    <Head title="Invoices" />

    <Heading title="Invoices" description="Manage your invoices" />

    <div class="mb-4 flex items-center justify-between gap-4">
        <div class="flex items-center gap-2">
            <Input
                class="max-w-xs"
                placeholder="Search invoices..."
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
                    <SelectItem value="draft">Draft</SelectItem>
                    <SelectItem value="sent">Sent</SelectItem>
                    <SelectItem value="partial">Partial</SelectItem>
                    <SelectItem value="paid">Paid</SelectItem>
                    <SelectItem value="overdue">Overdue</SelectItem>
                    <SelectItem value="cancelled">Cancelled</SelectItem>
                </SelectContent>
            </Select>
        </div>
        <Link :href="createRoute()">
            <Button>
                <Plus class="mr-1 h-4 w-4" />
                Create Invoice
            </Button>
        </Link>
    </div>

    <AppTable
        :columns="[
            { key: 'invoice_number', label: 'Invoice #' },
            { key: 'customer', label: 'Customer' },
            { key: 'invoice_date', label: 'Date' },
            { key: 'due_date', label: 'Due Date' },
            { key: 'grand_total', label: 'Total' },
            { key: 'status', label: 'Status' },
        ]"
        :data="invoices.data"
    >
        <template #cell="{ column, row }">
            <template v-if="column.key === 'customer'">
                {{ row.customer?.name ?? '-' }}
            </template>
            <template v-else-if="column.key === 'status'">
                <Badge :variant="getStatusVariant(row.status)">
                    {{ row.status }}
                </Badge>
            </template>
            <template v-else>
                {{ row[column.key as keyof typeof row] }}
            </template>
        </template>
        <template #actions="{ row }">
            <div class="flex items-center gap-1">
                <Button variant="ghost" size="icon-sm" as-child>
                    <Link :href="showRoute(Number(row.id))">
                        <Eye class="h-4 w-4" />
                    </Link>
                </Button>
                <Button variant="ghost" size="icon-sm" as-child>
                    <Link :href="editRoute(Number(row.id))">
                        <Pencil class="h-4 w-4" />
                    </Link>
                </Button>
                <AppConfirmDialog
                    :title="'Delete ' + (row.invoice_number ?? '') + '?'"
                    description="This will permanently delete this invoice."
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

    <div v-if="invoices?.meta?.total > 0" class="mt-4">
        <AppPagination :links="invoices.links" :meta="invoices.meta" />
    </div>
    </div>
</template>
