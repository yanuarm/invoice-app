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
import { index, create, store } from '@/routes/products';

const form = useForm({
    sku: '',
    name: '',
    description: '',
    unit: '',
    price: '',
    status: 'active',
});

function submit() {
    form.post(store.url());
}

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard() },
            { title: 'Products', href: index() },
            { title: 'Create', href: create() },
        ],
    },
});
</script>

<template>
    <Head title="Create Product" />

    <Heading title="Create Product" description="Add a new product" />

    <div class="max-w-2xl">
        <AppForm
            submit-label="Create Product"
            :loading="form.processing"
            :disabled="form.processing"
            @submit="submit"
            @cancel="() => router.get(index.url())"
        >
            <div class="grid gap-4 md:grid-cols-2">
                <div class="space-y-2">
                    <Label for="sku">SKU</Label>
                    <Input id="sku" v-model="form.sku" placeholder="Auto-generated" />
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
                <Input id="name" v-model="form.name" placeholder="Product name" />
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
                <Textarea id="description" v-model="form.description" placeholder="Product description" />
                <InputError :message="form.errors.description" />
            </div>
        </AppForm>
    </div>
</template>
