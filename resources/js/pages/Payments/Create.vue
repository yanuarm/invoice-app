<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import AppForm from '@/components/AppForm.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
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
import { Textarea } from '@/components/ui/textarea';
import { dashboard } from '@/routes';
import { index, create, store } from '@/routes/payments';

type Invoice = {
    id: number;
    invoice_number: string;
    grand_total: string;
    status: string;
    customer: { id: number; name: string };
};

type Props = {
    invoices: Invoice[];
};

const props = defineProps<Props>();

const form = useForm({
    invoice_id: '' as number | string,
    payment_date: new Date().toISOString().split('T')[0],
    amount: '',
    method: '' as string,
    reference_number: '',
    notes: '',
});

const methodOptions = [
    { value: 'cash', label: 'Cash' },
    { value: 'bank_transfer', label: 'Bank Transfer' },
    { value: 'credit_card', label: 'Credit Card' },
    { value: 'e_wallet', label: 'E-Wallet' },
    { value: 'other', label: 'Other' },
];

const statusColors: Record<string, string> = {
    sent: 'default',
    partial: 'warning',
};

function submit() {
    form.post(store.url());
}

function selectedInvoice() {
    return props.invoices.find((i) => String(i.id) === String(form.invoice_id));
}

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard() },
            { title: 'Payments', href: index() },
            { title: 'Create', href: create() },
        ],
    },
});
</script>

<template>
    <Head title="Record Payment" />

    <Heading title="Record Payment" description="Record a payment against an invoice" />

    <div class="max-w-2xl">
        <AppForm
            submit-label="Record Payment"
            :loading="form.processing"
            :disabled="form.processing"
            @submit="submit"
            @cancel="() => router.get(index.url())"
        >
            <div class="space-y-2">
                <Label for="invoice_id">Invoice</Label>
                <Select v-model="form.invoice_id">
                    <SelectTrigger>
                        <SelectValue placeholder="Select invoice" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem
                            v-for="invoice in invoices"
                            :key="invoice.id"
                            :value="invoice.id"
                        >
                            {{ invoice.invoice_number }} - {{ invoice.customer?.name }} ({{ invoice.grand_total }})
                        </SelectItem>
                    </SelectContent>
                </Select>
                <InputError :message="form.errors.invoice_id" />
            </div>

            <div v-if="selectedInvoice()" class="rounded-md border p-3 text-sm">
                <div class="flex items-center justify-between">
                    <span class="text-muted-foreground">Total</span>
                    <span class="font-medium">{{ selectedInvoice()!.grand_total }}</span>
                </div>
                <div class="flex items-center justify-between mt-1">
                    <span class="text-muted-foreground">Status</span>
                    <Badge :variant="statusColors[selectedInvoice()!.status] ?? 'secondary'">
                        {{ selectedInvoice()!.status }}
                    </Badge>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div class="space-y-2">
                    <Label for="payment_date">Payment Date</Label>
                    <Input id="payment_date" v-model="form.payment_date" type="date" />
                    <InputError :message="form.errors.payment_date" />
                </div>

                <div class="space-y-2">
                    <Label for="amount">Amount</Label>
                    <Input id="amount" v-model="form.amount" type="number" step="0.01" min="0.01" placeholder="0.00" />
                    <InputError :message="form.errors.amount" />
                </div>
            </div>

            <div class="space-y-2">
                <Label for="method">Payment Method</Label>
                <Select v-model="form.method">
                    <SelectTrigger>
                        <SelectValue placeholder="Select method" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem v-for="opt in methodOptions" :key="opt.value" :value="opt.value">
                            {{ opt.label }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                <InputError :message="form.errors.method" />
            </div>

            <div class="space-y-2">
                <Label for="reference_number">Reference Number</Label>
                <Input id="reference_number" v-model="form.reference_number" placeholder="Optional" />
                <InputError :message="form.errors.reference_number" />
            </div>

            <div class="space-y-2">
                <Label for="notes">Notes</Label>
                <Textarea id="notes" v-model="form.notes" placeholder="Optional notes" />
                <InputError :message="form.errors.notes" />
            </div>
        </AppForm>
    </div>
</template>
