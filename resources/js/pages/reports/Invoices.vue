<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { dashboard } from '@/routes';
import { show as invoiceShow } from '@/routes/invoices';

type Customer = { id: number; name: string };

type Invoice = {
    id: number;
    invoice_number: string;
    invoice_date: string;
    due_date: string;
    status: string;
    grand_total: string;
    customer: { id: number; name: string } | null;
    creator: { id: number; name: string } | null;
};

type Props = {
    invoices: { data: Invoice[] };
    totalAmount: number;
    paidAmount: number;
    customers: Customer[];
    filters: { status: string | null; customer_id: string | null; date_from: string | null; date_to: string | null };
};

const props = defineProps<Props>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard() },
            { title: 'Invoice Report', href: '#' },
        ],
    },
});

const status = ref(props.filters.status ?? '');
const customerId = ref(props.filters.customer_id ?? '');
const dateFrom = ref(props.filters.date_from ?? '');
const dateTo = ref(props.filters.date_to ?? '');
const processing = ref(false);

const statusOptions = ['draft', 'sent', 'partial', 'paid', 'overdue', 'cancelled'];

const statusColors: Record<string, string> = {
    draft: 'secondary',
    sent: 'default',
    partial: 'warning',
    paid: 'success',
    overdue: 'destructive',
    cancelled: 'outline',
};

function getStatusVariant(s: string): string {
    return statusColors[s] ?? 'secondary';
}

function filter() {
    processing.value = true;
    router.get('/reports/invoices', {
        status: status.value,
        customer_id: customerId.value,
        date_from: dateFrom.value,
        date_to: dateTo.value,
    }, {
        preserveState: true,
        preserveScroll: true,
        onFinish: () => { processing.value = false; },
    });
}

function reset() {
    status.value = '';
    customerId.value = '';
    dateFrom.value = '';
    dateTo.value = '';
    filter();
}

function formatCurrency(value: number | string): string {
    const num = typeof value === 'string' ? parseFloat(value) : value;
    return 'Rp ' + num.toLocaleString('id-ID');
}
</script>

<template>
    <div class="flex flex-col gap-6 p-6">
    <Head title="Invoice Report" />

    <Heading title="Invoice Report" description="Overview of all invoices" />

    <div class="grid gap-4 md:grid-cols-4">
        <Card>
            <CardHeader class="pb-2">
                <CardTitle class="text-sm font-medium">Total Amount</CardTitle>
            </CardHeader>
            <CardContent>
                <div class="text-2xl font-bold">{{ formatCurrency(totalAmount) }}</div>
            </CardContent>
        </Card>
        <Card>
            <CardHeader class="pb-2">
                <CardTitle class="text-sm font-medium">Paid Amount</CardTitle>
            </CardHeader>
            <CardContent>
                <div class="text-2xl font-bold">{{ formatCurrency(paidAmount) }}</div>
            </CardContent>
        </Card>
    </div>

    <Card class="mt-6">
        <CardHeader>
            <CardTitle>Filters</CardTitle>
        </CardHeader>
        <CardContent>
            <div class="flex flex-wrap items-end gap-4">
                <div class="space-y-2">
                    <Label for="status">Status</Label>
                    <Select v-model="status">
                        <SelectTrigger id="status" class="w-40">
                            <SelectValue placeholder="All Status" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="">All Status</SelectItem>
                            <SelectItem v-for="s in statusOptions" :key="s" :value="s">{{ s }}</SelectItem>
                        </SelectContent>
                    </Select>
                </div>
                <div class="space-y-2">
                    <Label for="customer_id">Customer</Label>
                    <Select v-model="customerId">
                        <SelectTrigger id="customer_id" class="w-48">
                            <SelectValue placeholder="All Customers" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="">All Customers</SelectItem>
                            <SelectItem v-for="c in customers" :key="c.id" :value="String(c.id)">{{ c.name }}</SelectItem>
                        </SelectContent>
                    </Select>
                </div>
                <div class="space-y-2">
                    <Label for="date_from">Date From</Label>
                    <Input id="date_from" v-model="dateFrom" type="date" />
                </div>
                <div class="space-y-2">
                    <Label for="date_to">Date To</Label>
                    <Input id="date_to" v-model="dateTo" type="date" />
                </div>
                <Button @click="filter" :disabled="processing">Apply</Button>
                <Button variant="outline" @click="reset">Reset</Button>
            </div>
        </CardContent>
    </Card>

    <div class="mt-6 flex justify-end gap-2">
        <a :href="`/reports/invoices/export-pdf?status=${status}&customer_id=${customerId}&date_from=${dateFrom}&date_to=${dateTo}`">
            <Button variant="outline">Export PDF</Button>
        </a>
        <a :href="`/reports/invoices/export-excel?status=${status}&customer_id=${customerId}&date_from=${dateFrom}&date_to=${dateTo}`">
            <Button variant="outline">Export Excel</Button>
        </a>
    </div>

    <Card class="mt-4">
        <CardContent class="p-0">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Invoice</TableHead>
                        <TableHead>Customer</TableHead>
                        <TableHead>Date</TableHead>
                        <TableHead>Due Date</TableHead>
                        <TableHead>Status</TableHead>
                        <TableHead class="text-right">Grand Total</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="inv in invoices.data" :key="inv.id">
                        <TableCell>
                            <Link :href="invoiceShow(inv.id)" class="font-medium hover:underline">
                                {{ inv.invoice_number }}
                            </Link>
                        </TableCell>
                        <TableCell>{{ inv.customer?.name ?? '-' }}</TableCell>
                        <TableCell>{{ inv.invoice_date }}</TableCell>
                        <TableCell>{{ inv.due_date }}</TableCell>
                        <TableCell>
                            <Badge :variant="getStatusVariant(inv.status)">
                                {{ inv.status }}
                            </Badge>
                        </TableCell>
                        <TableCell class="text-right font-medium">{{ formatCurrency(inv.grand_total) }}</TableCell>
                    </TableRow>
                    <TableRow v-if="invoices.data.length === 0">
                        <TableCell colspan="6" class="text-center text-muted-foreground py-4">
                            No invoices found.
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </CardContent>
    </Card>
    </div>
</template>
