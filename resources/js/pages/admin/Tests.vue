<script lang="ts" setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

// shadcn components
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle
} from '@/components/ui/alert-dialog';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
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
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';

import { Pencil, Plus, Trash2 } from 'lucide-vue-next';

const props = defineProps<{
    settings: { passMark: number };
    subjects: Array<{ id: number; name: string; link_url: string }>;
}>();

const breadcrumbs = [{ title: 'Tes', href: route('admin.tests.index') }];

// ====== LOCAL ALERT (tanpa flash) ======
const bannerMsg = ref<string>('');
const showBanner = computed(() => bannerMsg.value.length > 0);
function notify(msg: string) {
    bannerMsg.value = msg;
    // auto-hide optional:
    window.clearTimeout((notify as any)._t);
    (notify as any)._t = window.setTimeout(() => (bannerMsg.value = ''), 3500);
}

// ====== KKM ======
const settingsForm = useForm({
    pass_mark: props.settings?.passMark ?? 70,
});
function saveSettings() {
    settingsForm.post(route('admin.tests.settings.update'), {
        preserveScroll: true,
        onSuccess: () => {
            notify('KKM berhasil disimpan.');
            // segarkan data agar props terbaru (opsional, biasanya redirect sudah re-fetch)
            router.reload({ only: ['settings'], preserveScroll: true });
        },
    });
}

// ====== Subjects ======
type Subject = { id?: number; name: string; link_url: string };

const showModal = ref(false);
const editing = ref<Subject | null>(null);

const subjectForm = useForm<Subject>({
    id: undefined,
    name: '',
    link_url: '',
});

function openCreate() {
    editing.value = null;
    subjectForm.reset();
    showModal.value = true;
}
function openEdit(s: Subject) {
    editing.value = s;
    subjectForm.id = s.id;
    subjectForm.name = s.name;
    subjectForm.link_url = s.link_url;
    showModal.value = true;
}
function submitSubject() {
    if (editing.value?.id) {
        subjectForm.put(route('admin.tests.subjects.update', editing.value.id), {
            preserveScroll: true,
            onSuccess: () => {
                showModal.value = false;
                notify('Mata pelajaran diperbarui.');
                router.reload({ only: ['subjects'], preserveScroll: true });
            },
        });
    } else {
        subjectForm.post(route('admin.tests.subjects.store'), {
            preserveScroll: true,
            onSuccess: () => {
                showModal.value = false;
                notify('Mata pelajaran ditambahkan.');
                router.reload({ only: ['subjects'], preserveScroll: true });
            },
        });
    }
}

// delete
const showDelete = ref(false);
const deletingId = ref<number | null>(null);
function askDelete(id: number) {
    deletingId.value = id;
    showDelete.value = true;
}
function confirmDelete() {
    if (!deletingId.value) return;
    router.delete(route('admin.tests.subjects.destroy', deletingId.value), {
        preserveScroll: true,
        onSuccess: () => {
            showDelete.value = false;
            deletingId.value = null;
            notify('Mata pelajaran dihapus.');
            router.reload({ only: ['subjects'], preserveScroll: true });
        },
        onFinish: () => {
            // ensure dialog closed even if error handled elsewhere
            showDelete.value = false;
        },
    });
}
</script>

<template>
    <Head title="Kelola Tes" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <!-- Banner lokal tanpa flash -->
        <Alert v-if="showBanner" class="mx-4 mt-4 mb-0">
            <AlertTitle>Berhasil</AlertTitle>
            <AlertDescription>{{ bannerMsg }}</AlertDescription>
        </Alert>

        <div class="grid gap-4 p-4 md:grid-cols-2">
            <!-- KKM -->
            <Card>
                <CardHeader>
                    <CardTitle>KKM</CardTitle>
                    <CardDescription>Nilai kelulusan minimal (0–100).</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid max-w-xs gap-2">
                        <Label for="pass_mark">KKM</Label>
                        <Input id="pass_mark" v-model.number="settingsForm.pass_mark" max="100" min="0" placeholder="70" type="number" />
                        <p v-if="settingsForm.errors.pass_mark" class="text-sm text-red-600">
                            {{ settingsForm.errors.pass_mark }}
                        </p>
                    </div>
                </CardContent>
                <CardFooter>
                    <Button :disabled="settingsForm.processing" @click="saveSettings"> Simpan </Button>
                </CardFooter>
            </Card>

            <!-- Mata Pelajaran -->
            <Card class="md:col-span-1">
                <CardHeader class="flex-row items-center justify-between space-y-0">
                    <div>
                        <CardTitle>Mata Pelajaran</CardTitle>
                        <CardDescription>Kelola nama pelajaran & tautan tes.</CardDescription>
                    </div>
                    <Button size="sm" @click="openCreate"> <Plus class="mr-2 h-4 w-4" /> Tambah </Button>
                </CardHeader>
                <CardContent>
                    <div class="rounded-lg border">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead class="w-[48px] text-center">#</TableHead>
                                    <TableHead>Nama</TableHead>
                                    <TableHead>Tautan Tes</TableHead>
                                    <TableHead class="w-[140px] text-right">Aksi</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="(s, i) in props.subjects" :key="s.id">
                                    <TableCell class="text-center">{{ i + 1 }}</TableCell>
                                    <TableCell class="font-medium">{{ s.name }}</TableCell>
                                    <TableCell class="truncate">
                                        <a :href="s.link_url" class="text-emerald-700 hover:underline" rel="noopener" target="_blank">
                                            {{ s.link_url }}
                                        </a>
                                    </TableCell>
                                    <TableCell class="text-right">
                                        <div class="flex justify-end gap-2">
                                            <Button size="sm" variant="outline" @click="openEdit(s)"> <Pencil class="mr-1 h-4 w-4" /> Edit </Button>
                                            <Button size="sm" variant="destructive" @click="askDelete(s.id!)">
                                                <Trash2 class="mr-1 h-4 w-4" /> Hapus
                                            </Button>
                                        </div>
                                    </TableCell>
                                </TableRow>

                                <TableRow v-if="props.subjects.length === 0">
                                    <TableCell class="text-center text-muted-foreground" colspan="4"> Belum ada mata pelajaran. </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Modal: Tambah/Edit Subject -->
        <Dialog v-model:open="showModal">
            <DialogContent class="sm:max-w-lg">
                <DialogHeader>
                    <DialogTitle>{{ editing?.id ? 'Edit Mata Pelajaran' : 'Tambah Mata Pelajaran' }}</DialogTitle>
                    <DialogDescription>Lengkapi data di bawah ini.</DialogDescription>
                </DialogHeader>

                <div class="grid gap-4">
                    <div class="grid gap-2">
                        <Label for="name">Nama</Label>
                        <Input id="name" v-model="subjectForm.name" placeholder="Matematika" />
                        <p v-if="subjectForm.errors.name" class="text-sm text-red-600">{{ subjectForm.errors.name }}</p>
                    </div>

                    <div class="grid gap-2">
                        <Label for="link">Tautan Tes</Label>
                        <Input id="link" v-model="subjectForm.link_url" placeholder="https://contoh.com/ujian/matematika" />
                        <p v-if="subjectForm.errors.link_url" class="text-sm text-red-600">{{ subjectForm.errors.link_url }}</p>
                    </div>
                </div>

                <DialogFooter>
                    <Button variant="secondary" @click="showModal = false">Batal</Button>
                    <Button :disabled="subjectForm.processing" @click="submitSubject">
                        {{ editing?.id ? 'Simpan' : 'Tambah' }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <!-- Konfirmasi: Hapus Subject -->
        <AlertDialog v-model:open="showDelete">
            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle>Hapus mata pelajaran?</AlertDialogTitle>
                    <AlertDialogDescription>Tindakan ini tidak dapat dibatalkan.</AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel>Batal</AlertDialogCancel>
                    <AlertDialogAction @click="confirmDelete">Hapus</AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    </AppLayout>
</template>
