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
import { index, create, store } from '@/routes/customers';

const form = useForm({
    code: '',
    name: '',
    email: '',
    phone: '',
    address: '',
    tax_number: '',
    status: 'active',
});

function submit() {
    form.post(store.url());
}

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard() },
            { title: 'Customers', href: index() },
            { title: 'Create', href: create() },
        ],
    },
});
</script>

<template>
    <Head title="Create Customer" />

    <Heading title="Create Customer" description="Add a new customer" />

    <div class="max-w-2xl">
        <AppForm
            submit-label="Create Customer"
            :loading="form.processing"
            :disabled="form.processing"
            @submit="submit"
            @cancel="() => router.get(index.url())"
        >
            <div class="grid gap-4 md:grid-cols-2">
                <div class="space-y-2">
                    <Label for="code">Code</Label>
                    <Input id="code" v-model="form.code" placeholder="Auto-generated" />
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
                <Input id="name" v-model="form.name" placeholder="Customer name" />
                <InputError :message="form.errors.name" />
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div class="space-y-2">
                    <Label for="email">Email</Label>
                    <Input id="email" v-model="form.email" type="email" placeholder="customer@example.com" />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="space-y-2">
                    <Label for="phone">Phone</Label>
                    <Input id="phone" v-model="form.phone" placeholder="+62 xxx" />
                    <InputError :message="form.errors.phone" />
                </div>
            </div>

            <div class="space-y-2">
                <Label for="address">Address</Label>
                <Textarea id="address" v-model="form.address" placeholder="Customer address" />
                <InputError :message="form.errors.address" />
            </div>

            <div class="space-y-2">
                <Label for="tax_number">Tax Number</Label>
                <Input id="tax_number" v-model="form.tax_number" placeholder="Tax ID / NPWP" />
                <InputError :message="form.errors.tax_number" />
            </div>
        </AppForm>
    </div>
</template>
