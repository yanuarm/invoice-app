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
import { index, edit } from '@/routes/products';

type Product = {
    id: number;
    sku: string;
    name: string;
    description: string | null;
    unit: string;
    price: string;
    status: string;
    created_at: string;
    updated_at: string;
    creator: {
        id: number;
        name: string;
    } | null;
};

type Props = {
    product: Product;
};

defineProps<Props>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard() },
            { title: 'Products', href: index() },
            { title: 'Detail', href: '#' },
        ],
    },
});
</script>

<template>
    <div class="flex flex-col gap-6 p-6">
    <Head :title="product.name" />

    <div class="flex items-center justify-between">
        <Heading title="Product Detail" :description="product.sku" variant="small" />
        <Link :href="edit(product.id)">
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
                    <span class="font-medium">{{ product.name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">SKU</span>
                    <span class="font-medium">{{ product.sku }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">Status</span>
                    <Badge :variant="product.status === 'active' ? 'default' : 'secondary'">
                        {{ product.status }}
                    </Badge>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">Unit</span>
                    <span class="font-medium">{{ product.unit }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">Price</span>
                    <span class="font-medium">{{ product.price }}</span>
                </div>
            </CardContent>
        </Card>

        <Card>
            <CardHeader>
                <CardTitle>Additional</CardTitle>
            </CardHeader>
            <CardContent class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-muted-foreground">Description</span>
                    <span class="font-medium text-right max-w-[200px]">{{ product.description ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">Created by</span>
                    <span class="font-medium">{{ product.creator?.name ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">Created at</span>
                    <span class="font-medium">{{ product.created_at }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">Updated at</span>
                    <span class="font-medium">{{ product.updated_at }}</span>
                </div>
            </CardContent>
        </Card>
    </div>
    </div>
</template>
