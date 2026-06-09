<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { dashboard } from '@/routes';

type Settings = {
    company_name: string | null;
    company_email: string | null;
    company_phone: string | null;
    company_address: string | null;
    company_logo: string | null;
    invoice_prefix: string | null;
    invoice_footer: string | null;
};

type Props = {
    settings: Settings;
};

const props = defineProps<Props>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard() },
            { title: 'Company Profile', href: '#' },
        ],
    },
});

const form = useForm({
    company_name: props.settings.company_name ?? '',
    company_email: props.settings.company_email ?? '',
    company_phone: props.settings.company_phone ?? '',
    company_address: props.settings.company_address ?? '',
    invoice_prefix: props.settings.invoice_prefix ?? 'INV',
    invoice_footer: props.settings.invoice_footer ?? '',
});

function submit() {
    form.patch('/settings/company', {
        preserveScroll: true,
    });
}
</script>

<template>
    <div class="flex flex-col gap-6 p-6">
    <Head title="Company Profile" />

    <Heading title="Company Profile" description="Manage your company information" />

    <form @submit.prevent="submit" class="space-y-6">
        <Card>
            <CardHeader>
                <CardTitle>Company Information</CardTitle>
            </CardHeader>
            <CardContent class="space-y-4">
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="space-y-2">
                        <Label for="company_name">Company Name</Label>
                        <Input id="company_name" v-model="form.company_name" placeholder="My Company" />
                    </div>
                    <div class="space-y-2">
                        <Label for="company_email">Email</Label>
                        <Input id="company_email" v-model="form.company_email" type="email" placeholder="company@example.com" />
                    </div>
                    <div class="space-y-2">
                        <Label for="company_phone">Phone</Label>
                        <Input id="company_phone" v-model="form.company_phone" placeholder="+62 21 1234 5678" />
                    </div>
                    <div class="space-y-2">
                        <Label for="invoice_prefix">Invoice Prefix</Label>
                        <Input id="invoice_prefix" v-model="form.invoice_prefix" placeholder="INV" />
                        <p class="text-xs text-muted-foreground">
                            Example: INV-202606-0001
                        </p>
                    </div>
                </div>
                <div class="space-y-2">
                    <Label for="company_address">Address</Label>
                    <Textarea id="company_address" v-model="form.company_address" placeholder="123 Business Street, Jakarta" rows="3" />
                </div>
                <div class="space-y-2">
                    <Label for="invoice_footer">Invoice Footer</Label>
                    <Textarea id="invoice_footer" v-model="form.invoice_footer" placeholder="Thank you for your business!" rows="2" />
                </div>
            </CardContent>
        </Card>

        <div class="flex justify-end gap-4">
            <Button type="submit" :disabled="form.processing">
                Save Changes
            </Button>
        </div>
    </form>
    </div>
</template>
