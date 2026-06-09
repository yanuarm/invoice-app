<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Download, FileText, Pencil } from '@lucide/vue';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
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
import { download, index, edit, pdf } from '@/routes/invoices';

type Payment = {
    id: number;
    amount: string;
    payment_date: string;
    method: string;
    reference_number: string | null;
    creator: { id: number; name: string } | null;
};

type InvoiceItem = {
    id: number;
    product: { id: number; name: string };
    description: string | null;
    qty: string;
    price: string;
    discount: string;
    total: string;
};

type Invoice = {
    id: number;
    invoice_number: string;
    customer: { id: number; name: string } | null;
    invoice_date: string;
    due_date: string;
    subtotal: string;
    discount_amount: string;
    tax_amount: string;
    grand_total: string;
    status: string;
    notes: string | null;
    created_at: string;
    updated_at: string;
    creator: { id: number; name: string } | null;
    items: InvoiceItem[];
    payments: Payment[];
};

type Props = {
    invoice: Invoice;
};

const props = defineProps<Props>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard() },
            { title: 'Invoices', href: index() },
            { title: 'Detail', href: '#' },
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

function getStatusVariant(status: string): string {
    return statusColors[status] ?? 'secondary';
}
</script>

<template>
    <Head :title="invoice.invoice_number" />

    <div class="flex items-center justify-between">
        <Heading title="Invoice Detail" :description="invoice.invoice_number" variant="small" />
        <div class="flex gap-2">
            <a :href="pdf(invoice.id)" target="_blank">
                <Button variant="outline">
                    <FileText class="mr-1 h-4 w-4" />
                    PDF
                </Button>
            </a>
            <a :href="download(invoice.id)">
                <Button variant="outline">
                    <Download class="mr-1 h-4 w-4" />
                    Download
                </Button>
            </a>
            <Link :href="edit(invoice.id)">
                <Button variant="outline">
                    <Pencil class="mr-1 h-4 w-4" />
                    Edit
                </Button>
            </Link>
        </div>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <Card>
            <CardHeader>
                <CardTitle>Invoice Information</CardTitle>
            </CardHeader>
            <CardContent class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-muted-foreground">Invoice #</span>
                    <span class="font-medium">{{ invoice.invoice_number }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">Customer</span>
                    <span class="font-medium">{{ invoice.customer?.name ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">Status</span>
                    <Badge :variant="getStatusVariant(invoice.status)">
                        {{ invoice.status }}
                    </Badge>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">Invoice Date</span>
                    <span class="font-medium">{{ invoice.invoice_date }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">Due Date</span>
                    <span class="font-medium">{{ invoice.due_date }}</span>
                </div>
            </CardContent>
        </Card>

        <Card>
            <CardHeader>
                <CardTitle>Summary</CardTitle>
            </CardHeader>
            <CardContent class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-muted-foreground">Subtotal</span>
                    <span class="font-medium">{{ invoice.subtotal }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">Discount</span>
                    <span class="font-medium">{{ invoice.discount_amount }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">Tax (11%)</span>
                    <span class="font-medium">{{ invoice.tax_amount }}</span>
                </div>
                <div class="flex justify-between border-t pt-2">
                    <span class="font-semibold">Grand Total</span>
                    <span class="font-bold">{{ invoice.grand_total }}</span>
                </div>
            </CardContent>
        </Card>
    </div>

    <Card class="mt-6">
        <CardHeader>
            <CardTitle>Items</CardTitle>
        </CardHeader>
        <CardContent>
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Product</TableHead>
                        <TableHead>Description</TableHead>
                        <TableHead class="text-right">Qty</TableHead>
                        <TableHead class="text-right">Price</TableHead>
                        <TableHead class="text-right">Discount</TableHead>
                        <TableHead class="text-right">Total</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="item in invoice.items" :key="item.id">
                        <TableCell class="font-medium">{{ item.product?.name ?? '-' }}</TableCell>
                        <TableCell>{{ item.description ?? '-' }}</TableCell>
                        <TableCell class="text-right">{{ item.qty }}</TableCell>
                        <TableCell class="text-right">{{ item.price }}</TableCell>
                        <TableCell class="text-right">{{ item.discount }}</TableCell>
                        <TableCell class="text-right font-medium">{{ item.total }}</TableCell>
                    </TableRow>
                </TableBody>
            </Table>

            <div v-if="invoice.notes" class="mt-4">
                <p class="text-sm text-muted-foreground">Notes:</p>
                <p class="text-sm">{{ invoice.notes }}</p>
            </div>
        </CardContent>
    </Card>

    <Card class="mt-6">
        <CardHeader>
            <CardTitle>Payments</CardTitle>
        </CardHeader>
        <CardContent>
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Date</TableHead>
                        <TableHead class="text-right">Amount</TableHead>
                        <TableHead>Method</TableHead>
                        <TableHead>Reference</TableHead>
                        <TableHead>Recorded by</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="pm in invoice.payments" :key="pm.id">
                        <TableCell>{{ pm.payment_date }}</TableCell>
                        <TableCell class="text-right font-medium">{{ pm.amount }}</TableCell>
                        <TableCell>{{ pm.method }}</TableCell>
                        <TableCell>{{ pm.reference_number ?? '-' }}</TableCell>
                        <TableCell>{{ pm.creator?.name ?? '-' }}</TableCell>
                    </TableRow>
                    <TableRow v-if="!invoice.payments || invoice.payments.length === 0">
                        <TableCell colspan="5" class="text-center text-muted-foreground py-4">
                            No payments recorded yet.
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </CardContent>
    </Card>
</template>
