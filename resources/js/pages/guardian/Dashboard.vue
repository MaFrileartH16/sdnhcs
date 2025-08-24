<script lang="ts" setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';

import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Layers, Users } from 'lucide-vue-next';

const props = defineProps<{
    summary: {
        myStudents: number;
        myAdmissions: number;
        subjectsCount: number;
        passMark: number;
        completed: number;
        pending: number;
        overallAvg: number | null;
        lastStatus: 'Lulus Tes' | 'Tidak Lulus Tes' | '-';
        completionRate: number;
    };
}>();

const breadcrumbs = [{ title: 'Beranda', href: '/guardian/dashboard' }];

function statusVariant(s: string) {
    if (s === 'Lulus Tes') return 'default';
    if (s === 'Tidak Lulus Tes') return 'destructive';
    return 'secondary';
}
</script>

<template>
    <Head title="Beranda" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <!-- Baris KPI besar -->
            <div class="grid gap-4 md:grid-cols-3">
                <Card>
                    <CardHeader class="pb-2">
                        <CardDescription>Anak Saya</CardDescription>
                        <div class="flex items-baseline gap-2">
                            <Users class="h-5 w-5 text-muted-foreground" />
                            <CardTitle class="text-4xl">{{ props.summary.myStudents }}</CardTitle>
                        </div>
                    </CardHeader>
                    <CardContent class="text-sm text-muted-foreground">Terhubung ke akun Anda</CardContent>
                </Card>

                <Card>
                    <CardHeader class="pb-2">
                        <CardDescription>Formulir</CardDescription>
                        <div class="flex items-baseline gap-2">
                            <Layers class="h-5 w-5 text-muted-foreground" />
                            <CardTitle class="text-4xl">{{ props.summary.myAdmissions }}</CardTitle>
                        </div>
                    </CardHeader>
                    <CardContent class="text-sm text-muted-foreground">Total admission yang dibuat</CardContent>
                </Card>

                <Card>
                    <CardHeader class="pb-2">
                        <CardDescription>KKM & Mapel</CardDescription>
                        <div class="flex items-center gap-6">
                            <div>
                                <div class="text-xs text-muted-foreground">KKM</div>
                                <CardTitle class="text-3xl">{{ props.summary.passMark }}</CardTitle>
                            </div>
                            <div>
                                <div class="text-xs text-muted-foreground">Mata Pelajaran</div>
                                <CardTitle class="text-3xl">{{ props.summary.subjectsCount }}</CardTitle>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent class="text-sm text-muted-foreground">Acuan kelulusan & jumlah tes</CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
