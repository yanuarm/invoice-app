<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { dashboard } from '@/routes';
import { index } from '@/routes/payments';

type Payment = {
    id: number;
    invoice: {
        id: number;
        invoice_number: string;
        grand_total: string;
        status: string;
        customer: { id: number; name: string };
    };
    payment_date: string;
    amount: string;
    method: string;
    reference_number: string | null;
    notes: string | null;
    created_at: string;
    creator: { id: number; name: string };
};

type Props = {
    payment: Payment;
};

defineProps<Props>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard() },
            { title: 'Payments', href: index() },
            { title: 'Detail', href: '#' },
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

const statusColors: Record<string, string> = {
    draft: 'secondary',
    sent: 'default',
    partial: 'warning',
    paid: 'success',
    overdue: 'destructive',
    cancelled: 'outline',
};
</script>

<template>
    <div class="flex flex-col gap-6 p-6">
    <Head title="Payment Detail" />

    <div class="flex items-center justify-between">
        <Heading title="Payment Detail" :description="'Payment #' + payment.id" variant="small" />
        <Link :href="route('invoices.show', payment.invoice.id)">
            <Button variant="outline">
                View Invoice
            </Button>
        </Link>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <Card>
            <CardHeader>
                <CardTitle>Payment Information</CardTitle>
            </CardHeader>
            <CardContent class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-muted-foreground">Payment Date</span>
                    <span class="font-medium">{{ payment.payment_date }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">Amount</span>
                    <span class="font-bold">{{ payment.amount }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">Method</span>
                    <span class="font-medium">{{ methodLabels[payment.method] ?? payment.method }}</span>
                </div>
                <div v-if="payment.reference_number" class="flex justify-between">
                    <span class="text-muted-foreground">Reference</span>
                    <span class="font-medium">{{ payment.reference_number }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">Recorded by</span>
                    <span class="font-medium">{{ payment.creator?.name ?? '-' }}</span>
                </div>
            </CardContent>
        </Card>

        <Card>
            <CardHeader>
                <CardTitle>Invoice</CardTitle>
            </CardHeader>
            <CardContent class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-muted-foreground">Invoice #</span>
                    <Link :href="route('invoices.show', payment.invoice.id)" class="font-medium hover:underline">
                        {{ payment.invoice.invoice_number }}
                    </Link>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">Customer</span>
                    <span class="font-medium">{{ payment.invoice.customer?.name ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">Invoice Total</span>
                    <span class="font-medium">{{ payment.invoice.grand_total }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">Invoice Status</span>
                    <Badge :variant="statusColors[payment.invoice.status] ?? 'secondary'">
                        {{ payment.invoice.status }}
                    </Badge>
                </div>
            </CardContent>
        </Card>
    </div>

    <Card v-if="payment.notes" class="mt-6">
        <CardHeader>
            <CardTitle>Notes</CardTitle>
        </CardHeader>
        <CardContent>
            <p class="text-sm">{{ payment.notes }}</p>
        </CardContent>
    </Card>
    </div>
</template>
