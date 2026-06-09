<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
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
import { show as customerShow } from '@/routes/customers';

type Customer = {
    id: number;
    name: string;
    email: string | null;
    phone: string | null;
    total_invoices: number;
    total_revenue: number;
};

type Props = {
    customers: { data: Customer[] };
    filters: { search: string | null; sort: string | null };
};

const props = defineProps<Props>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard() },
            { title: 'Customer Report', href: '#' },
        ],
    },
});

const search = ref(props.filters.search ?? '');
const processing = ref(false);

function filter() {
    processing.value = true;
    router.get('/reports/customers', { search: search.value }, {
        preserveState: true,
        preserveScroll: true,
        onFinish: () => { processing.value = false; },
    });
}

function reset() {
    search.value = '';
    filter();
}

function formatCurrency(value: number | string): string {
    const num = typeof value === 'string' ? parseFloat(value) : value;
    return 'Rp ' + num.toLocaleString('id-ID');
}
</script>

<template>
    <div class="flex flex-col gap-6 p-6">
    <Head title="Customer Report" />

    <Heading title="Customer Report" description="Customer performance summary" />

    <Card>
        <CardHeader>
            <CardTitle>Filters</CardTitle>
        </CardHeader>
        <CardContent>
            <div class="flex flex-wrap items-end gap-4">
                <div class="space-y-2">
                    <Input id="search" v-model="search" placeholder="Search customers..." @keyup.enter="filter" />
                </div>
                <Button @click="filter" :disabled="processing">Search</Button>
                <Button variant="outline" @click="reset">Reset</Button>
            </div>
        </CardContent>
    </Card>

    <div class="mt-6 flex justify-end gap-2">
        <a href="/reports/customers/export-pdf">
            <Button variant="outline">Export PDF</Button>
        </a>
        <a href="/reports/customers/export-excel">
            <Button variant="outline">Export Excel</Button>
        </a>
    </div>

    <Card class="mt-4">
        <CardContent class="p-0">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Name</TableHead>
                        <TableHead>Email</TableHead>
                        <TableHead>Phone</TableHead>
                        <TableHead class="text-right">Total Invoices</TableHead>
                        <TableHead class="text-right">Total Revenue</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="c in customers.data" :key="c.id">
                        <TableCell>
                            <Link :href="customerShow(c.id)" class="font-medium hover:underline">
                                {{ c.name }}
                            </Link>
                        </TableCell>
                        <TableCell>{{ c.email ?? '-' }}</TableCell>
                        <TableCell>{{ c.phone ?? '-' }}</TableCell>
                        <TableCell class="text-right">{{ c.total_invoices }}</TableCell>
                        <TableCell class="text-right font-medium">{{ formatCurrency(c.total_revenue) }}</TableCell>
                    </TableRow>
                    <TableRow v-if="customers.data.length === 0">
                        <TableCell colspan="5" class="text-center text-muted-foreground py-4">
                            No customers found.
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </CardContent>
    </Card>
    </div>
</template>
