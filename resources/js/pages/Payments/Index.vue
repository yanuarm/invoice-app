<script setup lang="ts">
import { Head, router, Link } from '@inertiajs/vue3';
import { Eye, Plus, Trash2 } from '@lucide/vue';
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
import { index, show as showRoute, create as createRoute, destroy as destroyRoute } from '@/routes/payments';

type Payment = {
    id: number;
    invoice: { id: number; invoice_number: string; grand_total: string; status: string };
    payment_date: string;
    amount: string;
    method: string;
    reference_number: string | null;
    creator: { id: number; name: string };
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
    payments: {
        data: Payment[];
        links: PaginationLink[];
        meta: PaginationMeta;
    };
    filters: {
        search?: string;
        method?: string;
        sort?: string;
        direction?: string;
    };
};

defineProps<Props>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard() },
            { title: 'Payments', href: index() },
        ],
    },
});

const methodLabels: Record<string, string> = {
    cash: 'Cash',
    bank_transfer: 'Bank Transfer',
    credit_card: 'Credit Card',
    e_wallet: 'E-Wallet',
    other: 'Other',
};

function destroy(paymentId: number) {
    router.delete(destroyRoute.url(paymentId));
}
</script>

<template>
    <div class="flex flex-col gap-6 p-6">
    <Head title="Payments" />

    <Heading title="Payments" description="Record and manage payments" />

    <div class="mb-4 flex items-center justify-between gap-4">
        <div class="flex items-center gap-2">
            <Input
                class="max-w-xs"
                placeholder="Search payments..."
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
                :model-value="filters.method ?? 'all'"
                @update:model-value="                    
                    (value: unknown) =>
                    {
                        const method = value as string 
                        router.get(
                            index.url(),
                            method === 'all' 
                                ? {}
                                :  { method },
                            { preserveState: true, replace: true },
                        )
                    }
                "
            >
                <SelectTrigger class="w-[160px]">
                    <SelectValue placeholder="All methods" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem value="all">All methods</SelectItem>
                    <SelectItem v-for="(label, key) in methodLabels" :key="key" :value="key">
                        {{ label }}
                    </SelectItem>
                </SelectContent>
            </Select>
        </div>
        <Link :href="createRoute()">
            <Button>
                <Plus class="mr-1 h-4 w-4" />
                Record Payment
            </Button>
        </Link>
    </div>

    <AppTable
        :columns="[
            { key: 'invoice_number', label: 'Invoice #' },
            { key: 'payment_date', label: 'Date' },
            { key: 'method', label: 'Method' },
            { key: 'amount', label: 'Amount' },
            { key: 'reference_number', label: 'Reference' },
        ]"
        :data="payments.data"
    >
        <template #cell="{ column, row }">
            <template v-if="column.key === 'invoice_number'">
                <Link :href="route('invoices.show', row.invoice.id)" class="font-medium hover:underline">
                    {{ row.invoice?.invoice_number ?? '-' }}
                </Link>
            </template>
            <template v-else-if="column.key === 'method'">
                {{ methodLabels[row.method] ?? row.method }}
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
                <AppConfirmDialog
                    :title="'Delete payment #' + row.id + '?'"
                    description="Invoice status will be recalculated."
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

    <div v-if="payments?.meta?.total > 0" class="mt-4">
        <AppPagination :links="payments.links" :meta="payments.meta" />
    </div>
    </div>
</template>
