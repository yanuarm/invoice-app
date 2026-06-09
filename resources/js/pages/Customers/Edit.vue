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
import { index, update } from '@/routes/customers';

type Customer = {
    id: number;
    code: string;
    name: string;
    email: string | null;
    phone: string | null;
    address: string | null;
    tax_number: string | null;
    status: string;
};

type Props = {
    customer: Customer;
};

const props = defineProps<Props>();

const form = useForm({
    code: props.customer.code,
    name: props.customer.name,
    email: props.customer.email ?? '',
    phone: props.customer.phone ?? '',
    address: props.customer.address ?? '',
    tax_number: props.customer.tax_number ?? '',
    status: props.customer.status,
});

function submit() {
    form.patch(update.url(props.customer.id));
}

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard() },
            { title: 'Customers', href: index() },
            { title: 'Edit', href: '#' },
        ],
    },
});
</script>

<template>
    <Head title="Edit Customer" />

    <Heading title="Edit Customer" :description="customer.name" />

    <div class="max-w-2xl">
        <AppForm
            submit-label="Update Customer"
            :loading="form.processing"
            :disabled="form.processing"
            @submit="submit"
            @cancel="() => router.get(index.url())"
        >
            <div class="grid gap-4 md:grid-cols-2">
                <div class="space-y-2">
                    <Label for="code">Code</Label>
                    <Input id="code" v-model="form.code" />
                    <InputError :message="form.errors.code" />
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
                    <Label for="email">Email</Label>
                    <Input id="email" v-model="form.email" type="email" />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="space-y-2">
                    <Label for="phone">Phone</Label>
                    <Input id="phone" v-model="form.phone" />
                    <InputError :message="form.errors.phone" />
                </div>
            </div>

            <div class="space-y-2">
                <Label for="address">Address</Label>
                <Textarea id="address" v-model="form.address" />
                <InputError :message="form.errors.address" />
            </div>

            <div class="space-y-2">
                <Label for="tax_number">Tax Number</Label>
                <Input id="tax_number" v-model="form.tax_number" />
                <InputError :message="form.errors.tax_number" />
            </div>
        </AppForm>
    </div>
</template>
