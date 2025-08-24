<script lang="ts" setup>
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ClipboardList, Users } from 'lucide-vue-next';

const props = defineProps<{
    stats: {
        passMark: number;
        totalStudents: number;
        totalGuardians: number;
        totalAdmissions: number;
        subjectsCount: number;
        completeCount: number;
        passCount: number;
        failCount: number;
    };
    recent: Array<{
        id: number;
        student_name: string;
        guardian_name: string;
        guardian_phone: string;
        applied_at: string;
    }>;
}>();

const breadcrumbs = [{ title: 'Beranda', href: '/admin/dashboard' }];
</script>

<template>
    <Head title="Dashboard Admin" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <!-- Top Stats -->
            <div class="grid gap-4 md:grid-cols-3">
                <Card>
                    <CardHeader class="pb-2">
                        <CardDescription>Jumlah Siswa</CardDescription>
                        <CardTitle class="text-3xl">{{ props.stats.totalStudents }}</CardTitle>
                    </CardHeader>
                    <CardContent class="text-sm text-muted-foreground">Terdaftar di sistem</CardContent>
                </Card>

                <Card>
                    <CardHeader class="pb-2">
                        <CardDescription>Jumlah Wali</CardDescription>
                        <CardTitle class="text-3xl">{{ props.stats.totalGuardians }}</CardTitle>
                    </CardHeader>
                    <CardContent class="text-sm text-muted-foreground">Akun role guardian</CardContent>
                </Card>

                <Card>
                    <CardHeader class="pb-2">
                        <CardDescription>Formulir Masuk</CardDescription>
                        <CardTitle class="text-3xl">{{ props.stats.totalAdmissions }}</CardTitle>
                    </CardHeader>
                    <CardContent class="text-sm text-muted-foreground">Admission tersimpan</CardContent>
                </Card>
            </div>

            <!-- Mid Stats -->
            <div class="grid gap-4 md:grid-cols-3">
                <Card>
                    <CardHeader class="pb-2">
                        <CardDescription>KKM</CardDescription>
                        <CardTitle class="text-3xl">{{ props.stats.passMark }}</CardTitle>
                    </CardHeader>
                    <CardContent class="text-sm text-muted-foreground">Nilai lulus minimal</CardContent>
                </Card>

                <Card>
                    <CardHeader class="pb-2">
                        <CardDescription>Mata Pelajaran</CardDescription>
                        <CardTitle class="text-3xl">{{ props.stats.subjectsCount }}</CardTitle>
                    </CardHeader>
                    <CardContent class="text-sm text-muted-foreground">Tersedia untuk tes</CardContent>
                </Card>

                <Card>
                    <CardHeader class="pb-2">
                        <CardDescription>Ujian Selesai</CardDescription>
                        <CardTitle class="text-3xl">{{ props.stats.completeCount }}</CardTitle>
                    </CardHeader>
                    <CardContent class="text-sm text-muted-foreground">Semua mata pelajaran terisi</CardContent>
                </Card>
            </div>

            <!-- Pass / Fail -->
            <div class="grid gap-4 md:grid-cols-2">
                <Card>
                    <CardHeader class="pb-2">
                        <CardDescription>Lulus Tes</CardDescription>
                        <CardTitle class="text-3xl">{{ props.stats.passCount }}</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Badge class="px-2 py-1" variant="default">≥ KKM</Badge>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="pb-2">
                        <CardDescription>Tidak Lulus Tes</CardDescription>
                        <CardTitle class="text-3xl">{{ props.stats.failCount }}</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Badge class="px-2 py-1" variant="destructive">&lt; KKM</Badge>
                    </CardContent>
                </Card>
            </div>

            <!-- Recent Applicants -->
            <Card>
                <CardHeader class="flex-row items-center justify-between space-y-0">
                    <div>
                        <CardTitle>Pendaftar Terbaru</CardTitle>
                        <CardDescription>5 pendaftar terakhir</CardDescription>
                    </div>
                    <div class="flex gap-2">
                        <Button as-child size="sm" variant="outline">
                            <Link href="/admin/applicants"><Users class="mr-2 h-4 w-4" /> Lihat Pendaftar</Link>
                        </Button>
                        <Button as-child class="bg-emerald-600 text-white hover:bg-emerald-700" size="sm">
                            <Link href="/admin/tests"><ClipboardList class="mr-2 h-4 w-4" /> Kelola Tes</Link>
                        </Button>
                    </div>
                </CardHeader>
                <CardContent>
                    <div class="rounded-lg border">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead class="w-[48px] text-center">#</TableHead>
                                    <TableHead>Siswa</TableHead>
                                    <TableHead>Wali</TableHead>
                                    <TableHead>Telepon</TableHead>
                                    <TableHead>Waktu Daftar</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="(r, i) in props.recent" :key="r.id">
                                    <TableCell class="text-center">{{ i + 1 }}</TableCell>
                                    <TableCell class="font-medium">{{ r.student_name }}</TableCell>
                                    <TableCell>{{ r.guardian_name }}</TableCell>
                                    <TableCell>{{ r.guardian_phone }}</TableCell>
                                    <TableCell>{{ r.applied_at }}</TableCell>
                                </TableRow>
                                <TableRow v-if="props.recent.length === 0">
                                    <TableCell class="text-center text-muted-foreground" colspan="5">Belum ada data.</TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
