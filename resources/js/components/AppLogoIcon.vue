<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { cn } from '@/lib/utils';

defineOptions({
    inheritAttrs: false,
});

type Props = {
    className?: HTMLAttributes['class'];
};

defineProps<Props>();
</script>

<template>
    <img
        :class="cn('object-contain', className)"
        src="/logo/logo-iksass.png"
        alt="Logo IKSASS"
        v-bind="$attrs"
    />
</template>
