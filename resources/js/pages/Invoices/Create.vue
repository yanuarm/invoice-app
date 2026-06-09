<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { Plus, Trash2 } from '@lucide/vue';
import AppForm from '@/components/AppForm.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { Textarea } from '@/components/ui/textarea';
import { dashboard } from '@/routes';
import { index, create, store } from '@/routes/invoices';

type Customer = {
    id: number;
    name: string;
};

type Product = {
    id: number;
    name: string;
    price: string;
    unit: string;
};

type ItemRow = {
    product_id: number | null;
    description: string;
    qty: string;
    price: string;
    discount: string;
};

type Props = {
    customers: Customer[];
    products: Product[];
};

const props = defineProps<Props>();

const form = useForm({
    customer_id: '' as number | string,
    invoice_date: new Date().toISOString().split('T')[0],
    due_date: '',
    discount_amount: '0',
    status: 'draft',
    notes: '',
    items: [] as ItemRow[],
});

function addItem() {
    form.items.push({
        product_id: null,
        description: '',
        qty: '1',
        price: '0',
        discount: '0',
    });
}

function removeItem(index: number) {
    form.items.splice(index, 1);
}

function onProductSelect(index: number, productId: string) {
    form.items[index].product_id = Number(productId);

    const product = props.products.find((p) => String(p.id) === productId);
    if (product && parseFloat(form.items[index].price) === 0) {
        form.items[index].price = product.price;
        form.items[index].description = product.name;
    }
}

function getItemTotal(index: number): number {
    const item = form.items[index];
    const qty = parseFloat(item.qty) || 0;
    const price = parseFloat(item.price) || 0;
    const discount = parseFloat(item.discount) || 0;
    return qty * price - discount;
}

function calculateSubtotal(): number {
    return form.items.reduce((sum, _, i) => sum + getItemTotal(i), 0);
}

function calculateTax(): number {
    return calculateSubtotal() * 0.11;
}

function calculateGrandTotal(): number {
    const subtotal = calculateSubtotal();
    const discount = parseFloat(form.discount_amount) || 0;
    const tax = calculateTax();
    return subtotal - discount + tax;
}

function submit() {
    form.post(store.url());
}

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard() },
            { title: 'Invoices', href: index() },
            { title: 'Create', href: create() },
        ],
    },
});
</script>

<template>
    <div class="flex flex-col gap-6 p-6">
    <Head title="Create Invoice" />

    <Heading title="Create Invoice" description="Create a new invoice" />

    <div class="max-w-4xl">
        <AppForm
            submit-label="Save as Draft"
            :loading="form.processing"
            :disabled="form.processing"
            @submit="submit"
            @cancel="() => router.get(index.url())"
        >
            <div class="grid gap-4 md:grid-cols-3">
                <div class="space-y-2">
                    <Label for="customer_id">Customer</Label>
                    <Select v-model="form.customer_id">
                        <SelectTrigger>
                            <SelectValue placeholder="Select customer" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="customer in customers"
                                :key="customer.id"
                                :value="customer.id"
                            >
                                {{ customer.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.customer_id" />
                </div>

                <div class="space-y-2">
                    <Label for="invoice_date">Invoice Date</Label>
                    <Input id="invoice_date" v-model="form.invoice_date" type="date" />
                    <InputError :message="form.errors.invoice_date" />
                </div>

                <div class="space-y-2">
                    <Label for="due_date">Due Date</Label>
                    <Input id="due_date" v-model="form.due_date" type="date" />
                    <InputError :message="form.errors.due_date" />
                </div>
            </div>

            <div class="space-y-2">
                <Label for="status">Status</Label>
                <Select v-model="form.status">
                    <SelectTrigger>
                        <SelectValue placeholder="Select status" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="draft">Draft</SelectItem>
                        <SelectItem value="sent">Sent</SelectItem>
                    </SelectContent>
                </Select>
                <InputError :message="form.errors.status" />
            </div>

            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <Label>Items</Label>
                    <Button type="button" variant="outline" size="sm" @click="addItem">
                        <Plus class="mr-1 h-4 w-4" />
                        Add Item
                    </Button>
                </div>

                <InputError :message="form.errors.items" />

                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Product</TableHead>
                            <TableHead>Description</TableHead>
                            <TableHead class="text-right w-[100px]">Qty</TableHead>
                            <TableHead class="text-right w-[120px]">Price</TableHead>
                            <TableHead class="text-right w-[100px]">Disc</TableHead>
                            <TableHead class="text-right w-[120px]">Total</TableHead>
                            <TableHead class="w-[50px]"></TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="(item, i) in form.items" :key="i">
                            <TableCell>
                                <Select
                                    :model-value="String(item.product_id ?? '')"
                                    @update:model-value="(v: unknown) => onProductSelect(i, v as string)"
                                >
                                    <SelectTrigger>
                                        <SelectValue placeholder="Product" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem
                                            v-for="product in products"
                                            :key="product.id"
                                            :value="String(product.id)"
                                        >
                                            {{ product.name }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError :message="form.errors['items.' + i + '.product_id']" />
                            </TableCell>
                            <TableCell>
                                <Input v-model="item.description" placeholder="Desc" class="min-w-[150px]" />
                                <InputError :message="form.errors['items.' + i + '.description']" />
                            </TableCell>
                            <TableCell>
                                <Input v-model="item.qty" type="number" step="0.01" min="0.01" class="w-[90px] text-right" />
                                <InputError :message="form.errors['items.' + i + '.qty']" />
                            </TableCell>
                            <TableCell>
                                <Input v-model="item.price" type="number" step="0.01" min="0" class="w-[110px] text-right" />
                                <InputError :message="form.errors['items.' + i + '.price']" />
                            </TableCell>
                            <TableCell>
                                <Input v-model="item.discount" type="number" step="0.01" min="0" class="w-[90px] text-right" />
                                <InputError :message="form.errors['items.' + i + '.discount']" />
                            </TableCell>
                            <TableCell class="text-right font-medium">
                                {{ getItemTotal(i).toFixed(2) }}
                            </TableCell>
                            <TableCell>
                                <Button type="button" variant="ghost" size="icon-sm" @click="removeItem(i)">
                                    <Trash2 class="h-4 w-4" />
                                </Button>
                            </TableCell>
                        </TableRow>

                        <TableRow v-if="form.items.length === 0">
                            <TableCell colspan="7" class="text-center text-muted-foreground py-6">
                                No items. Click "Add Item" to add products.
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div class="space-y-2">
                    <Label for="notes">Notes</Label>
                    <Textarea id="notes" v-model="form.notes" placeholder="Optional notes" />
                    <InputError :message="form.errors.notes" />
                </div>

                <div class="space-y-3">
                    <div class="space-y-2">
                        <Label for="discount_amount">Discount Amount</Label>
                        <Input id="discount_amount" v-model="form.discount_amount" type="number" step="0.01" min="0" />
                        <InputError :message="form.errors.discount_amount" />
                    </div>

                    <div class="border-t pt-2 space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Subtotal</span>
                            <span>{{ calculateSubtotal().toFixed(2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Discount</span>
                            <span>{{ (parseFloat(form.discount_amount) || 0).toFixed(2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Tax (11%)</span>
                            <span>{{ calculateTax().toFixed(2) }}</span>
                        </div>
                        <div class="flex justify-between font-bold text-base">
                            <span>Grand Total</span>
                            <span>{{ calculateGrandTotal().toFixed(2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </AppForm>
    </div>
    </div>
</template>
