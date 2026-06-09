<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
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

type Payment = {
    id: number;
    payment_date: string;
    amount: string;
    method: string;
    reference_number: string | null;
    invoice: { id: number; invoice_number: string } | null;
    creator: { id: number; name: string } | null;
};

type Props = {
    payments: { data: Payment[] };
    totalRevenue: number;
    filters: { start_date: string | null; end_date: string | null };
};

const props = defineProps<Props>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard() },
            { title: 'Revenue Report', href: '#' },
        ],
    },
});

const startDate = ref(props.filters.start_date ?? '');
const endDate = ref(props.filters.end_date ?? '');
const processing = ref(false);

function filter() {
    processing.value = true;
    router.get('/reports/revenue', { start_date: startDate.value, end_date: endDate.value }, {
        preserveState: true,
        preserveScroll: true,
        onFinish: () => { processing.value = false; },
    });
}

function reset() {
    startDate.value = '';
    endDate.value = '';
    filter();
}

function formatCurrency(value: number | string): string {
    const num = typeof value === 'string' ? parseFloat(value) : value;
    return 'Rp ' + num.toLocaleString('id-ID');
}
</script>

<template>
    <div class="flex flex-col gap-6 p-6">
    <Head title="Revenue Report" />

    <Heading title="Revenue Report" description="Track your revenue over time" />

    <div class="grid gap-4 md:grid-cols-4">
        <Card>
            <CardHeader class="pb-2">
                <CardTitle class="text-sm font-medium">Total Revenue</CardTitle>
            </CardHeader>
            <CardContent>
                <div class="text-2xl font-bold">{{ formatCurrency(totalRevenue) }}</div>
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
                    <Label for="start_date">Start Date</Label>
                    <Input id="start_date" v-model="startDate" type="date" />
                </div>
                <div class="space-y-2">
                    <Label for="end_date">End Date</Label>
                    <Input id="end_date" v-model="endDate" type="date" />
                </div>
                <Button @click="filter" :disabled="processing">Apply</Button>
                <Button variant="outline" @click="reset">Reset</Button>
            </div>
        </CardContent>
    </Card>

    <div class="mt-6 flex justify-end gap-2">
        <a :href="`/reports/revenue/export-pdf?start_date=${startDate}&end_date=${endDate}`">
            <Button variant="outline">Export PDF</Button>
        </a>
        <a :href="`/reports/revenue/export-excel?start_date=${startDate}&end_date=${endDate}`">
            <Button variant="outline">Export Excel</Button>
        </a>
    </div>

    <Card class="mt-4">
        <CardContent class="p-0">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Date</TableHead>
                        <TableHead>Invoice</TableHead>
                        <TableHead>Method</TableHead>
                        <TableHead>Reference</TableHead>
                        <TableHead class="text-right">Amount</TableHead>
                        <TableHead>Recorded By</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="pm in payments.data" :key="pm.id">
                        <TableCell>{{ pm.payment_date }}</TableCell>
                        <TableCell>{{ pm.invoice?.invoice_number ?? '-' }}</TableCell>
                        <TableCell>{{ pm.method }}</TableCell>
                        <TableCell>{{ pm.reference_number ?? '-' }}</TableCell>
                        <TableCell class="text-right font-medium">{{ formatCurrency(pm.amount) }}</TableCell>
                        <TableCell>{{ pm.creator?.name ?? '-' }}</TableCell>
                    </TableRow>
                    <TableRow v-if="payments.data.length === 0">
                        <TableCell colspan="6" class="text-center text-muted-foreground py-4">
                            No payments found.
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </CardContent>
    </Card>
    </div>
</template>
