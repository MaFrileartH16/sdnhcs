<script lang="ts" setup>
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <img :class="className" alt="Logo Sekolah" src="/logo.png" v-bind="$attrs" />
</template>
