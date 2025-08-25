<script lang="ts" setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Pagination, PaginationContent, PaginationItem } from '@/components/ui/pagination';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { ClipboardList, ExternalLink, FileText, Search, Trash2 } from 'lucide-vue-next';

type Row = {
    id: number;
    student_name: string;
    guardian_name: string;
    guardian_phone: string;
    average_score: number | null;
    status: string;
    created_at: string | null;
    files: Array<{ label: string; path: string | null }>;
};
type Subject = { id: number; name: string; link_url: string };

const props = defineProps<{
    items: { data: (Row | null)[]; links: any[]; total: number };
    filters: { q?: string | null; perPage?: number | null };
    meta: { passMark: number; perPages: number[]; subjects: Subject[] };
}>();

const breadcrumbs = [{ title: 'Pendaftar', href: route('admin.applicants.index') }];
const rows = computed<Row[]>(() => (props.items.data || []).filter((r): r is Row => !!r));

const q = ref(props.filters.q ?? '');
const perPage = ref(String(props.filters.perPage ?? 10));

let t: number | undefined;
watch([q, perPage], () => {
    if (t) clearTimeout(t);
    t = window.setTimeout(() => {
        router.get(
            route('admin.applicants.index'),
            { q: q.value, perPage: perPage.value },
            { preserveScroll: true, preserveState: true, replace: true, only: ['items', 'filters', 'meta'] },
        );
    }, 300);
});

// dialog
const showFiles = ref(false);
const showSubjects = ref(false);
const activeRow = ref<Row | null>(null);

// hapus
const showDelete = ref(false);
const deleteId = ref<number | null>(null);

function openFiles(row: Row) {
    activeRow.value = row;
    showFiles.value = true;
}
function openSubjects(row: Row) {
    activeRow.value = row;
    showSubjects.value = true;
}

function askDelete(id: number) {
    deleteId.value = id;
    showDelete.value = true;
}
function confirmDelete() {
    if (!deleteId.value) return;
    router.delete(route('admin.applicants.destroy', deleteId.value), {
        preserveScroll: true,
        onFinish: () => {
            showDelete.value = false;
            deleteId.value = null;
        },
    });
}

function statusVariant(s: string) {
    if (s === 'Lulus Tes') return 'default';
    if (s === 'Tidak Lulus Tes') return 'destructive';
    return 'secondary';
}
</script>

<template>
    <Head title="Pendaftar" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <!-- Filters -->
            <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                <div class="relative w-full md:max-w-md">
                    <Search class="absolute top-1/2 left-2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                    <Input v-model="q" class="pl-8" placeholder="Cari siswa/wali/telepon…" />
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">Baris</Label>
                    <Select v-model="perPage">
                        <SelectTrigger class="w-[110px]">
                            <SelectValue :placeholder="String(props.filters.perPage ?? 10)" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="n in props.meta.perPages" :key="n" :value="String(n)">{{ n }}</SelectItem>
                        </SelectContent>
                    </Select>
                </div>
            </div>

            <!-- Table -->
            <div class="rounded-xl border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="w-[48px] text-center">#</TableHead>
                            <TableHead>Siswa</TableHead>
                            <TableHead>Wali</TableHead>
                            <TableHead>Telepon</TableHead>
                            <TableHead>Rata-rata</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead>Daftar</TableHead>
                            <TableHead class="w-[260px] text-right">Aksi</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="(row, i) in rows" :key="row.id">
                            <TableCell class="text-center">{{ i + 1 }}</TableCell>
                            <TableCell class="font-medium">{{ row.student_name }}</TableCell>
                            <TableCell>{{ row.guardian_name }}</TableCell>
                            <TableCell>{{ row.guardian_phone }}</TableCell>
                            <TableCell>{{ row.average_score ?? '-' }}</TableCell>
                            <TableCell
                                ><Badge :variant="statusVariant(row.status)">{{ row.status }}</Badge></TableCell
                            >
                            <TableCell>{{ row.created_at ?? '-' }}</TableCell>
                            <TableCell class="text-right">
                                <div class="flex justify-end gap-2">
                                    <Button size="sm" variant="outline" @click="openFiles(row)">
                                        <FileText class="mr-2 h-4 w-4" /> Lihat Berkas
                                    </Button>
                                    <Button class="bg-emerald-600 text-white hover:bg-emerald-700" size="sm" @click="openSubjects(row)">
                                        <ClipboardList class="mr-2 h-4 w-4" /> Kerjakan Tes
                                    </Button>
                                    <Button size="sm" variant="destructive" @click="askDelete(row.id)">
                                        <Trash2 class="mr-2 h-4 w-4" /> Hapus
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="rows.length === 0">
                            <TableCell class="text-center text-muted-foreground" colspan="8">Belum ada data siswa</TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <!-- Pagination -->
            <div v-if="props.items.links.length" class="flex items-center justify-center py-2">
                <Pagination>
                    <PaginationContent>
                        <PaginationItem v-for="(l, i) in props.items.links" :key="i">
                            <Link
                                v-if="l?.url"
                                :class="[l.active ? 'bg-emerald-600 text-white hover:bg-emerald-700' : 'hover:bg-muted']"
                                :href="l.url"
                                :preserve-scroll="true"
                                :preserve-state="true"
                                class="rounded-md px-3 py-1 text-sm transition-colors"
                                v-html="l.label"
                            />
                            <span v-else class="rounded-md px-3 py-1 text-sm text-muted-foreground opacity-50" v-html="l.label" />
                        </PaginationItem>
                    </PaginationContent>
                </Pagination>
            </div>
        </div>

        <!-- Modal: Lihat Berkas -->
        <Dialog v-model:open="showFiles">
            <DialogContent class="sm:max-w-lg">
                <DialogHeader>
                    <DialogTitle>Berkas Pendaftar</DialogTitle>
                    <DialogDescription>{{ activeRow?.student_name || '-' }}</DialogDescription>
                </DialogHeader>
                <div class="space-y-2">
                    <div v-if="(activeRow?.files?.length || 0) === 0" class="text-sm text-muted-foreground">Belum ada berkas.</div>
                    <ul v-else class="space-y-2">
                        <li v-for="(f, idx) in activeRow?.files" :key="idx" class="flex items-center justify-between rounded-md border p-2">
                            <span>{{ f.label }}</span>
                            <a
                                v-if="f.path"
                                :href="f.path"
                                class="inline-flex items-center text-emerald-700 hover:underline"
                                rel="noopener"
                                target="_blank"
                            >
                                Buka <ExternalLink class="ml-1 h-4 w-4" />
                            </a>
                            <span v-else class="text-sm text-muted-foreground">-</span>
                        </li>
                    </ul>
                </div>
                <DialogFooter><Button variant="secondary" @click="showFiles = false">Tutup</Button></DialogFooter>
            </DialogContent>
        </Dialog>

        <!-- Modal: Kerjakan Tes -->
        <Dialog v-model:open="showSubjects">
            <DialogContent class="sm:max-w-lg">
                <DialogHeader>
                    <DialogTitle>Daftar Tes</DialogTitle>
                    <DialogDescription>{{ activeRow?.student_name || '-' }} — KKM: {{ props.meta.passMark }}</DialogDescription>
                </DialogHeader>
                <div class="space-y-2">
                    <ul class="space-y-2">
                        <li v-for="s in props.meta.subjects" :key="s.id" class="flex items-center justify-between rounded-md border p-2">
                            <span>{{ s.name }}</span>
                            <a :href="s.link_url" class="inline-flex items-center text-emerald-700 hover:underline" rel="noopener" target="_blank">
                                Buka Tes <ExternalLink class="ml-1 h-4 w-4" />
                            </a>
                        </li>
                    </ul>
                </div>
                <DialogFooter><Button variant="secondary" @click="showSubjects = false">Tutup</Button></DialogFooter>
            </DialogContent>
        </Dialog>

        <!-- Modal: Konfirmasi Hapus -->
        <Dialog v-model:open="showDelete">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>Hapus Pendaftar</DialogTitle>
                    <DialogDescription>Apakah Anda yakin ingin menghapus pendaftar ini? Tindakan tidak dapat dibatalkan.</DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button variant="secondary" @click="showDelete = false">Batal</Button>
                    <Button variant="destructive" @click="confirmDelete">Hapus</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
