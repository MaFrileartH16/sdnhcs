<script lang="ts" setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';

import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Progress } from '@/components/ui/progress';
import { CheckCircle2, Clock3, GraduationCap, Layers, Users } from 'lucide-vue-next';

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
    <Head title="Dashboard Wali - Ringkasan" />

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

            <!-- Baris Progres & Rerata -->
            <div class="grid gap-4 md:grid-cols-3">
                <Card class="md:col-span-2">
                    <CardHeader class="pb-2">
                        <CardDescription>Progres Tes</CardDescription>
                        <div class="flex items-center gap-6">
                            <div class="flex items-baseline gap-2">
                                <CheckCircle2 class="h-5 w-5 text-muted-foreground" />
                                <div>
                                    <div class="text-xs text-muted-foreground">Selesai</div>
                                    <div class="text-2xl font-semibold">{{ props.summary.completed }}</div>
                                </div>
                            </div>
                            <div class="flex items-baseline gap-2">
                                <Clock3 class="h-5 w-5 text-muted-foreground" />
                                <div>
                                    <div class="text-xs text-muted-foreground">Menunggu</div>
                                    <div class="text-2xl font-semibold">{{ props.summary.pending }}</div>
                                </div>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="mb-2 flex items-center justify-between text-sm">
                            <span>Tingkat Penyelesaian</span>
                            <span class="font-medium">{{ props.summary.completionRate }}%</span>
                        </div>
                        <Progress :model-value="props.summary.completionRate" />
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="pb-2">
                        <CardDescription>Rata-rata & Status</CardDescription>
                        <div class="flex items-center gap-6">
                            <div>
                                <div class="text-xs text-muted-foreground">Rata-rata</div>
                                <div class="text-3xl font-semibold">
                                    {{ props.summary.overallAvg ?? '-' }}
                                </div>
                            </div>
                            <div>
                                <div class="text-xs text-muted-foreground">Status Terakhir</div>
                                <Badge :variant="statusVariant(props.summary.lastStatus)">
                                    <GraduationCap class="mr-1 h-4 w-4" />
                                    {{ props.summary.lastStatus }}
                                </Badge>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent class="text-sm text-muted-foreground"> Status terakhir dihitung dari tes lengkap terbaru. </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
