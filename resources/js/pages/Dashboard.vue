<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Bar, Doughnut } from 'vue-chartjs';
import {
    ArcElement,
    BarElement,
    CategoryScale,
    Chart as ChartJS,
    Legend,
    LinearScale,
    Title,
    Tooltip,
} from 'chart.js';
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
import { show as invoiceShow } from '@/routes/invoices';
import { show as paymentShow } from '@/routes/payments';

ChartJS.register(
    ArcElement,
    BarElement,
    CategoryScale,
    Legend,
    LinearScale,
    Title,
    Tooltip,
);

type Stats = {
    total_revenue: number;
    total_invoices: number;
    outstanding_invoices: number;
    paid_invoices: number;
};

type RevenueMonth = {
    month: string;
    revenue: number;
};

type StatusItem = {
    status: string;
    count: number;
};

type Invoice = {
    id: number;
    invoice_number: string;
    customer: { id: number; name: string } | null;
    grand_total: string;
    status: string;
    created_at: string;
};

type Payment = {
    id: number;
    amount: string;
    payment_date: string;
    method: string;
    invoice: { id: number; invoice_number: string } | null;
    creator: { id: number; name: string } | null;
};

type Props = {
    stats: Stats;
    revenuePerMonth: RevenueMonth[];
    statusDistribution: StatusItem[];
    latestInvoices: Invoice[];
    latestPayments: Payment[];
};

const props = defineProps<Props>();

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

const barData = {
    labels: props.revenuePerMonth.map((r) => r.month),
    datasets: [
        {
            label: 'Revenue',
            backgroundColor: '#3b82f6',
            borderRadius: 6,
            data: props.revenuePerMonth.map((r) => r.revenue),
        },
    ],
};

const barOptions = {
    responsive: true,
    plugins: {
        legend: { display: false },
    },
    scales: {
        y: {
            beginAtZero: true,
            ticks: {
                callback: (value: number) =>
                    'Rp ' + value.toLocaleString('id-ID'),
            },
        },
    },
};

const doughnutData = {
    labels: props.statusDistribution.map((s) => s.status),
    datasets: [
        {
            backgroundColor: ['#6b7280', '#3b82f6', '#f59e0b', '#10b981', '#ef4444'],
            data: props.statusDistribution.map((s) => s.count),
        },
    ],
};

const doughnutOptions = {
    responsive: true,
    plugins: {
        legend: {
            position: 'bottom' as const,
        },
    },
};

function formatCurrency(value: number | string): string {
    const num = typeof value === 'string' ? parseFloat(value) : value;
    return 'Rp ' + num.toLocaleString('id-ID');
}
</script>

<template>
    <Head title="Dashboard" />

    <Heading title="Dashboard" description="Overview of your business" />

    <div class="grid gap-4 md:grid-cols-4">
        <Card>
            <CardHeader class="flex flex-row items-center justify-between pb-2">
                <CardTitle class="text-sm font-medium">Total Revenue</CardTitle>
                <span class="text-2xl">💰</span>
            </CardHeader>
            <CardContent>
                <div class="text-2xl font-bold">{{ formatCurrency(stats.total_revenue) }}</div>
            </CardContent>
        </Card>

        <Card>
            <CardHeader class="flex flex-row items-center justify-between pb-2">
                <CardTitle class="text-sm font-medium">Total Invoices</CardTitle>
                <span class="text-2xl">📄</span>
            </CardHeader>
            <CardContent>
                <div class="text-2xl font-bold">{{ stats.total_invoices }}</div>
            </CardContent>
        </Card>

        <Card>
            <CardHeader class="flex flex-row items-center justify-between pb-2">
                <CardTitle class="text-sm font-medium">Outstanding</CardTitle>
                <span class="text-2xl">⏳</span>
            </CardHeader>
            <CardContent>
                <div class="text-2xl font-bold">{{ stats.outstanding_invoices }}</div>
            </CardContent>
        </Card>

        <Card>
            <CardHeader class="flex flex-row items-center justify-between pb-2">
                <CardTitle class="text-sm font-medium">Paid</CardTitle>
                <span class="text-2xl">✅</span>
            </CardHeader>
            <CardContent>
                <div class="text-2xl font-bold">{{ stats.paid_invoices }}</div>
            </CardContent>
        </Card>
    </div>

    <div class="mt-6 grid gap-6 md:grid-cols-2">
        <Card>
            <CardHeader>
                <CardTitle>Revenue Per Month</CardTitle>
            </CardHeader>
            <CardContent>
                <Bar v-if="revenuePerMonth.length > 0" :data="barData" :options="barOptions" />
                <p v-else class="text-sm text-muted-foreground">No revenue data yet.</p>
            </CardContent>
        </Card>

        <Card>
            <CardHeader>
                <CardTitle>Invoice Status</CardTitle>
            </CardHeader>
            <CardContent>
                <Doughnut v-if="statusDistribution.length > 0" :data="doughnutData" :options="doughnutOptions" />
                <p v-else class="text-sm text-muted-foreground">No invoice data yet.</p>
            </CardContent>
        </Card>
    </div>

    <div class="mt-6 grid gap-6 md:grid-cols-2">
        <Card>
            <CardHeader class="flex items-center justify-between">
                <CardTitle>Latest Invoices</CardTitle>
                <Link href="/invoices">
                    <Button variant="outline" size="sm">View All</Button>
                </Link>
            </CardHeader>
            <CardContent>
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Invoice</TableHead>
                            <TableHead>Customer</TableHead>
                            <TableHead class="text-right">Amount</TableHead>
                            <TableHead>Status</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="inv in latestInvoices" :key="inv.id">
                            <TableCell>
                                <Link :href="invoiceShow(inv.id)" class="font-medium hover:underline">
                                    {{ inv.invoice_number }}
                                </Link>
                            </TableCell>
                            <TableCell>{{ inv.customer?.name ?? '-' }}</TableCell>
                            <TableCell class="text-right">{{ formatCurrency(inv.grand_total) }}</TableCell>
                            <TableCell>
                                <Badge :variant="getStatusVariant(inv.status)">
                                    {{ inv.status }}
                                </Badge>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="latestInvoices.length === 0">
                            <TableCell colspan="4" class="text-center text-muted-foreground py-4">
                                No invoices yet.
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </CardContent>
        </Card>

        <Card>
            <CardHeader class="flex items-center justify-between">
                <CardTitle>Latest Payments</CardTitle>
                <Link href="/payments">
                    <Button variant="outline" size="sm">View All</Button>
                </Link>
            </CardHeader>
            <CardContent>
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Invoice</TableHead>
                            <TableHead class="text-right">Amount</TableHead>
                            <TableHead>Method</TableHead>
                            <TableHead>Date</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="pm in latestPayments" :key="pm.id">
                            <TableCell>
                                <Link :href="paymentShow(pm.id)" class="font-medium hover:underline">
                                    {{ pm.invoice?.invoice_number ?? '-' }}
                                </Link>
                            </TableCell>
                            <TableCell class="text-right">{{ formatCurrency(pm.amount) }}</TableCell>
                            <TableCell>{{ pm.method }}</TableCell>
                            <TableCell>{{ pm.payment_date }}</TableCell>
                        </TableRow>
                        <TableRow v-if="latestPayments.length === 0">
                            <TableCell colspan="4" class="text-center text-muted-foreground py-4">
                                No payments yet.
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </CardContent>
        </Card>
    </div>
</template>
