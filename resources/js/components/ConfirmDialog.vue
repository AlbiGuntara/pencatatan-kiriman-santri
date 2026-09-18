<script setup lang="ts">
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';

withDefaults(
    defineProps<{
        title: string;
        description: string;
        confirmText?: string;
    }>(),
    {
        confirmText: 'Hapus',
    },
);

const emit = defineEmits<{
    confirm: [];
}>();

const open = ref(false);

const handleConfirm = () => {
    open.value = false;
    emit('confirm');
};
</script>

<template>
    <Dialog v-model:open="open">
        <DialogTrigger as-child>
            <slot />
        </DialogTrigger>
        <DialogContent>
            <DialogHeader class="space-y-3">
                <DialogTitle>{{ title }}</DialogTitle>
                <DialogDescription>{{ description }}</DialogDescription>
            </DialogHeader>
            <DialogFooter class="gap-2">
                <DialogClose as-child>
                    <Button variant="secondary">Batal</Button>
                </DialogClose>
                <Button variant="destructive" @click="handleConfirm">
                    {{ confirmText }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
