<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import AppForm from '@/components/AppForm.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
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
import { index, update } from '@/routes/products';

type Product = {
    id: number;
    sku: string;
    name: string;
    description: string | null;
    unit: string;
    price: string;
    status: string;
};

type Props = {
    product: Product;
};

const props = defineProps<Props>();

const form = useForm({
    sku: props.product.sku,
    name: props.product.name,
    description: props.product.description ?? '',
    unit: props.product.unit,
    price: props.product.price,
    status: props.product.status,
});

function submit() {
    form.patch(update.url(props.product.id));
}

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard() },
            { title: 'Products', href: index() },
            { title: 'Edit', href: '#' },
        ],
    },
});
</script>

<template>
    <Head title="Edit Product" />

    <Heading title="Edit Product" :description="product.name" />

    <div class="max-w-2xl">
        <AppForm
            submit-label="Update Product"
            :loading="form.processing"
            :disabled="form.processing"
            @submit="submit"
            @cancel="() => router.get(index.url())"
        >
            <div class="grid gap-4 md:grid-cols-2">
                <div class="space-y-2">
                    <Label for="sku">SKU</Label>
                    <Input id="sku" v-model="form.sku" />
                    <InputError :message="form.errors.sku" />
                </div>

                <div class="space-y-2">
                    <Label for="status">Status</Label>
                    <Select v-model="form.status">
                        <SelectTrigger>
                            <SelectValue placeholder="Select status" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="active">Active</SelectItem>
                            <SelectItem value="inactive">Inactive</SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.status" />
                </div>
            </div>

            <div class="space-y-2">
                <Label for="name">Name</Label>
                <Input id="name" v-model="form.name" />
                <InputError :message="form.errors.name" />
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div class="space-y-2">
                    <Label for="unit">Unit</Label>
                    <Select v-model="form.unit">
                        <SelectTrigger>
                            <SelectValue placeholder="Select unit" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="pcs">Pieces (pcs)</SelectItem>
                            <SelectItem value="kg">Kilogram (kg)</SelectItem>
                            <SelectItem value="liter">Liter</SelectItem>
                            <SelectItem value="meter">Meter</SelectItem>
                            <SelectItem value="box">Box</SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.unit" />
                </div>

                <div class="space-y-2">
                    <Label for="price">Price</Label>
                    <Input id="price" v-model="form.price" type="number" step="0.01" min="0" placeholder="0.00" />
                    <InputError :message="form.errors.price" />
                </div>
            </div>

            <div class="space-y-2">
                <Label for="description">Description</Label>
                <Textarea id="description" v-model="form.description" />
                <InputError :message="form.errors.description" />
            </div>
        </AppForm>
    </div>
</template>
