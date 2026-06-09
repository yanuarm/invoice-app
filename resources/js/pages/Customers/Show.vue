<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Pencil } from '@lucide/vue';
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
import { index, edit } from '@/routes/customers';

type Customer = {
    id: number;
    code: string;
    name: string;
    email: string | null;
    phone: string | null;
    address: string | null;
    tax_number: string | null;
    status: string;
    created_at: string;
    updated_at: string;
    creator: {
        id: number;
        name: string;
    } | null;
};

type Props = {
    customer: Customer;
};

defineProps<Props>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard() },
            { title: 'Customers', href: index() },
            { title: 'Detail', href: '#' },
        ],
    },
});
</script>

<template>
    <div class="flex flex-col gap-6 p-6">
    <Head :title="customer.name" />

    <div class="flex items-center justify-between">
        <Heading title="Customer Detail" :description="customer.code" variant="small" />
        <Link :href="edit(customer.id)">
            <Button variant="outline">
                <Pencil class="mr-1 h-4 w-4" />
                Edit
            </Button>
        </Link>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <Card>
            <CardHeader>
                <CardTitle>Information</CardTitle>
            </CardHeader>
            <CardContent class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-muted-foreground">Name</span>
                    <span class="font-medium">{{ customer.name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">Code</span>
                    <span class="font-medium">{{ customer.code }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">Status</span>
                    <Badge :variant="customer.status === 'active' ? 'default' : 'secondary'">
                        {{ customer.status }}
                    </Badge>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">Email</span>
                    <span class="font-medium">{{ customer.email ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">Phone</span>
                    <span class="font-medium">{{ customer.phone ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">Tax Number</span>
                    <span class="font-medium">{{ customer.tax_number ?? '-' }}</span>
                </div>
            </CardContent>
        </Card>

        <Card>
            <CardHeader>
                <CardTitle>Additional</CardTitle>
            </CardHeader>
            <CardContent class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-muted-foreground">Address</span>
                    <span class="font-medium text-right max-w-[200px]">{{ customer.address ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">Created by</span>
                    <span class="font-medium">{{ customer.creator?.name ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">Created at</span>
                    <span class="font-medium">{{ customer.created_at }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">Updated at</span>
                    <span class="font-medium">{{ customer.updated_at }}</span>
                </div>
            </CardContent>
        </Card>
    </div>
    </div>
</template>
