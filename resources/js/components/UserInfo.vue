<script setup lang="ts">
import { computed } from 'vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { useInitials } from '@/composables/useInitials';
import type { User } from '@/types';

type Props = {
    user: User;
    showEmail?: boolean;
    inverse?: boolean;
};

const props = withDefaults(defineProps<Props>(), {
    showEmail: false,
    inverse: false,
});

const { getInitials } = useInitials();

// Compute whether we should show the avatar image
const showAvatar = computed(
    () => props.user.avatar && props.user.avatar !== '',
);

const subtitle = computed(() => {
    if (props.showEmail) {
        return props.user.email;
    }

    const role = props.user.role_label ?? 'Admin';

    return props.user.region_name
        ? `${role} · ${props.user.region_name}`
        : role;
});
</script>

<template>
    <Avatar
        class="h-8 w-8 overflow-hidden rounded-lg bg-sidebar-primary text-sidebar-primary-foreground"
    >
        <AvatarImage v-if="showAvatar" :src="user.avatar!" :alt="user.name" />
        <AvatarFallback
            class="rounded-lg bg-sidebar-primary text-sidebar-primary-foreground"
        >
            {{ getInitials(user.name) }}
        </AvatarFallback>
    </Avatar>

    <div class="grid flex-1 text-left text-sm leading-tight">
        <span
            class="truncate font-medium"
            :class="inverse ? 'text-sidebar-accent-foreground' : ''"
        >
            {{ user.name }}
        </span>
        <span
            class="truncate text-xs"
            :class="
                inverse ? 'text-sidebar-foreground/70' : 'text-muted-foreground'
            "
        >
            {{ subtitle }}
        </span>
    </div>
</template>
