<script setup lang="ts">
import { LoaderCircle } from '@lucide/vue';
import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';

type Props = {
    submitLabel?: string;
    cancelLabel?: string;
    loading?: boolean;
    disabled?: boolean;
    class?: string;
};

const emits = defineEmits<{
    submit: [];
    cancel: [];
}>();

withDefaults(defineProps<Props>(), {
    submitLabel: 'Save',
    cancelLabel: 'Cancel',
});
</script>

<template>
    <form
        :class="cn('space-y-6', $props.class)"
        @submit.prevent="emits('submit')"
    >
        <slot />

        <div class="flex items-center gap-3">
            <Button type="submit" :disabled="loading || disabled">
                <LoaderCircle
                    v-if="loading"
                    class="mr-1 h-4 w-4 animate-spin"
                />
                {{ submitLabel }}
            </Button>
            <Button
                v-if="$props.cancelLabel"
                type="button"
                variant="ghost"
                :disabled="loading"
                @click="emits('cancel')"
            >
                {{ cancelLabel }}
            </Button>
        </div>
    </form>
</template>
