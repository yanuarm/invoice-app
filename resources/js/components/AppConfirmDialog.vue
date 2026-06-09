<script setup lang="ts">
import { AlertTriangle } from '@lucide/vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';

type Props = {
    title?: string;
    description?: string;
    confirmLabel?: string;
    cancelLabel?: string;
    variant?: 'destructive' | 'default';
    loading?: boolean;
};

const emits = defineEmits<{
    confirm: [];
}>();

withDefaults(defineProps<Props>(), {
    title: 'Are you sure?',
    description: 'This action cannot be undone.',
    confirmLabel: 'Confirm',
    cancelLabel: 'Cancel',
    variant: 'destructive',
});
</script>

<template>
    <Dialog>
        <DialogTrigger as-child>
            <slot name="trigger" />
        </DialogTrigger>
        <DialogContent>
            <DialogHeader>
                <div
                    v-if="variant === 'destructive'"
                    class="bg-destructive/10 mx-auto flex h-10 w-10 items-center justify-center rounded-full"
                >
                    <AlertTriangle class="text-destructive h-5 w-5" />
                </div>
                <DialogTitle class="text-center">
                    {{ title }}
                </DialogTitle>
                <DialogDescription class="text-center">
                    {{ description }}
                </DialogDescription>
            </DialogHeader>
            <DialogFooter class="sm:justify-center">
                <DialogClose as-child>
                    <Button variant="outline" :disabled="loading">
                        {{ cancelLabel }}
                    </Button>
                </DialogClose>
                <Button
                    :variant="variant"
                    :disabled="loading"
                    @click="emits('confirm')"
                >
                    {{ confirmLabel }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
