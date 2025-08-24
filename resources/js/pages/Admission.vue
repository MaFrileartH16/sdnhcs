<script lang="ts" setup>
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { Separator } from '@/components/ui/separator';
import { Sheet, SheetContent, SheetTrigger } from '@/components/ui/sheet';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { onMounted, ref, watch } from 'vue';

const nav = ref([
    { label: 'Beranda', hash: '#hero' },
    { label: 'Visi & Misi', hash: '#visi-misi' },
    { label: 'PPDB', hash: '#info-ppdb' },
    { label: 'Kontak', hash: '#kontak' },
]);

const ctas = ref([
    { label: 'Daftar PPDB', href: route('admissions.index'), variant: 'primary' },
    { label: 'Masuk Akun', href: route('login'), variant: 'outline' },
]);

const form = useForm({
    guardian_name: '',
    guardian_relation: 'father',
    phone: '',
    full_name: '',
    gender: 'male',
    admission_form_file: null as File | null,
    family_card_file: null as File | null,
    birth_certificate_file: null as File | null,
    guardian_id_card_file: null as File | null,
    kindergarten_certificate_file: null as File | null,
});

const page = usePage();
const flashSuccess = ref<string | null>(page.props.flash?.success ?? null);
const flashError = ref<string | null>(page.props.flash?.error ?? null);
let to: number | null = null;

const submit = () =>
    form.post(route('admissions.store'), {
        forceFormData: true,
        onSuccess: () => {
            flashSuccess.value = page.props.flash?.success || 'Pendaftaran berhasil. Anda akan diarahkan ke halaman masuk.';
            if (to) clearTimeout(to);
            to = window.setTimeout(() => router.visit(route('login')), 2500);
        },
        onError: () => {
            flashError.value = 'Gagal mengirim pendaftaran. Periksa kembali isian Anda.';
        },
    });

watch(
    () => page.props.flash,
    (v) => {
        if (v?.success) {
            flashSuccess.value = String(v.success);
            if (to) clearTimeout(to);
            to = window.setTimeout(() => router.visit(route('login')), 2500);
        }
        if (v?.error) flashError.value = String(v.error);
    },
    { deep: true },
);

onMounted(() => {
    if (flashSuccess.value) {
        to = window.setTimeout(() => router.visit(route('login')), 2500);
    }
});
</script>

<template>
    <Head title="Daftar PPDB" />
    <div
        class="min-h-screen bg-gradient-to-b from-emerald-50 via-yellow-50 to-white text-[#1b1b18] dark:from-emerald-900/30 dark:via-yellow-900/20 dark:to-[#0a0a0a] dark:text-[#EDEDEC]"
    >
        <header class="sticky top-0 z-40 border-b border-black/5 bg-white/70 backdrop-blur-md dark:border-white/10 dark:bg-[#0a0a0a]/70">
            <div class="mx-auto flex w-full max-w-6xl items-center justify-between px-6 py-3">
                <Link :href="route('home') + '#hero'" aria-label="Beranda" class="flex items-center">
                    <img alt="Logo Sekolah" class="h-10 w-10 object-contain" src="/logo.png" />
                </Link>

                <nav class="hidden items-center gap-1 text-sm md:flex">
                    <Button v-for="n in nav" :key="n.hash" asChild class="px-3 hover:bg-emerald-50 dark:hover:bg-emerald-900/30" variant="ghost">
                        <Link :href="route('home') + n.hash">{{ n.label }}</Link>
                    </Button>
                </nav>
                <div class="hidden items-center gap-2 md:flex">
                    <Button
                        v-for="c in ctas"
                        :key="c.label"
                        :class="
                            c.variant === 'outline'
                                ? 'hover:border-emerald-600 hover:text-emerald-700 dark:hover:text-emerald-300'
                                : 'bg-emerald-600 text-white shadow-sm hover:bg-emerald-700 dark:bg-emerald-400 dark:text-[#0a0a0a] dark:hover:bg-emerald-300'
                        "
                        :variant="c.variant === 'outline' ? 'outline' : undefined"
                        asChild
                    >
                        <Link :href="c.href">{{ c.label }}</Link>
                    </Button>
                </div>
                <div class="md:hidden">
                    <Sheet>
                        <SheetTrigger as-child><Button size="sm" variant="outline">Menu</Button></SheetTrigger>
                        <SheetContent class="w-72" side="right">
                            <div class="grid gap-4 py-6">
                                <div class="px-1 text-xs tracking-wide text-[#706f6c] uppercase dark:text-[#A1A09A]">Navigasi</div>
                                <Button v-for="n in nav" :key="n.hash" asChild class="justify-start" variant="ghost"
                                    ><Link :href="route('home') + n.hash">{{ n.label }}</Link></Button
                                >
                                <Separator class="my-2" />
                                <div class="px-1 text-xs tracking-wide text-[#706f6c] uppercase dark:text-[#A1A09A]">Aksi</div>
                                <Button
                                    v-for="c in ctas"
                                    :key="c.label + '-m'"
                                    :class="
                                        c.variant === 'outline'
                                            ? 'w-full'
                                            : 'w-full bg-emerald-600 text-white hover:bg-emerald-700 dark:bg-emerald-400 dark:text-[#0a0a0a] dark:hover:bg-emerald-300'
                                    "
                                    :variant="c.variant === 'outline' ? 'outline' : undefined"
                                    asChild
                                >
                                    <Link :href="c.href">{{ c.label }}</Link>
                                </Button>
                            </div>
                        </SheetContent>
                    </Sheet>
                </div>
            </div>
        </header>

        <main class="mx-auto w-full max-w-4xl px-6 py-10">
            <div v-if="flashSuccess || flashError" class="mb-6">
                <Alert
                    v-if="flashSuccess"
                    class="border-emerald-300/60 bg-emerald-50 text-emerald-900 dark:border-emerald-300/30 dark:bg-emerald-900/20 dark:text-emerald-100"
                >
                    <AlertTitle>Berhasil</AlertTitle>
                    <AlertDescription>{{ flashSuccess }}</AlertDescription>
                </Alert>
                <Alert v-else class="mt-4 border-red-300/60 bg-red-50 text-red-900 dark:border-red-300/30 dark:bg-red-900/20 dark:text-red-100">
                    <AlertTitle>Gagal</AlertTitle>
                    <AlertDescription>{{ flashError }}</AlertDescription>
                </Alert>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Form Pendaftaran PPDB</CardTitle>
                    <CardDescription>Isi data berikut dengan benar.</CardDescription>
                </CardHeader>
                <CardContent>
                    <form class="grid gap-6" @submit.prevent="submit">
                        <div class="grid gap-6 lg:grid-cols-2">
                            <div class="grid gap-3">
                                <h3 class="text-sm font-medium">Data Orang Tua/Wali</h3>
                                <div class="grid gap-2">
                                    <Label for="guardian_name">Nama Orang Tua/Wali</Label>
                                    <Input id="guardian_name" v-model="form.guardian_name" placeholder="Nama lengkap" required />
                                    <p v-if="form.errors.guardian_name" class="text-xs text-red-600">{{ form.errors.guardian_name }}</p>
                                </div>
                                <div class="grid gap-2">
                                    <Label>Hubungan</Label>
                                    <RadioGroup v-model="form.guardian_relation" class="flex gap-4">
                                        <div class="flex items-center gap-2">
                                            <RadioGroupItem id="father" value="father" /><Label for="father">Ayah</Label>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <RadioGroupItem id="mother" value="mother" /><Label for="mother">Ibu</Label>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <RadioGroupItem id="guardian" value="guardian" /><Label for="guardian">Wali</Label>
                                        </div>
                                    </RadioGroup>
                                    <p v-if="form.errors.guardian_relation" class="text-xs text-red-600">{{ form.errors.guardian_relation }}</p>
                                </div>
                                <div class="grid gap-2">
                                    <Label for="phone">Nomor HP (untuk akun)</Label>
                                    <Input id="phone" v-model="form.phone" placeholder="08xxxxxxxxxx" required type="tel" />
                                    <p v-if="form.errors.phone" class="text-xs text-red-600">{{ form.errors.phone }}</p>
                                </div>
                            </div>

                            <div class="grid gap-3">
                                <h3 class="text-sm font-medium">Data Siswa</h3>
                                <div class="grid gap-2">
                                    <Label for="full_name">Nama Lengkap Siswa</Label>
                                    <Input id="full_name" v-model="form.full_name" placeholder="Nama lengkap" required />
                                    <p v-if="form.errors.full_name" class="text-xs text-red-600">{{ form.errors.full_name }}</p>
                                </div>
                                <div class="grid gap-2">
                                    <Label>Jenis Kelamin</Label>
                                    <RadioGroup v-model="form.gender" class="flex gap-4">
                                        <div class="flex items-center gap-2">
                                            <RadioGroupItem id="male" value="male" /><Label for="male">Laki-laki</Label>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <RadioGroupItem id="female" value="female" /><Label for="female">Perempuan</Label>
                                        </div>
                                    </RadioGroup>
                                    <p v-if="form.errors.gender" class="text-xs text-red-600">{{ form.errors.gender }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="grid gap-3">
                            <h3 class="text-sm font-medium">Berkas Wajib (5)</h3>
                            <div class="grid gap-2 lg:grid-cols-2">
                                <div class="grid gap-2">
                                    <Label for="admission_form_file">Formulir Pendaftaran</Label>
                                    <Input
                                        id="admission_form_file"
                                        accept="image/*,application/pdf"
                                        required
                                        type="file"
                                        @change="(e) => (form.admission_form_file = (e.target as HTMLInputElement).files?.[0] ?? null)"
                                    />
                                    <p v-if="form.errors.admission_form_file" class="text-xs text-red-600">{{ form.errors.admission_form_file }}</p>
                                </div>
                                <div class="grid gap-2">
                                    <Label for="family_card_file">Kartu Keluarga</Label>
                                    <Input
                                        id="family_card_file"
                                        accept="image/*,application/pdf"
                                        required
                                        type="file"
                                        @change="(e) => (form.family_card_file = (e.target as HTMLInputElement).files?.[0] ?? null)"
                                    />
                                    <p v-if="form.errors.family_card_file" class="text-xs text-red-600">{{ form.errors.family_card_file }}</p>
                                </div>
                                <div class="grid gap-2">
                                    <Label for="birth_certificate_file">Akta Lahir</Label>
                                    <Input
                                        id="birth_certificate_file"
                                        accept="image/*,application/pdf"
                                        required
                                        type="file"
                                        @change="(e) => (form.birth_certificate_file = (e.target as HTMLInputElement).files?.[0] ?? null)"
                                    />
                                    <p v-if="form.errors.birth_certificate_file" class="text-xs text-red-600">
                                        {{ form.errors.birth_certificate_file }}
                                    </p>
                                </div>
                                <div class="grid gap-2">
                                    <Label for="guardian_id_card_file">KTP Orang Tua/Wali</Label>
                                    <Input
                                        id="guardian_id_card_file"
                                        accept="image/*,application/pdf"
                                        required
                                        type="file"
                                        @change="(e) => (form.guardian_id_card_file = (e.target as HTMLInputElement).files?.[0] ?? null)"
                                    />
                                    <p v-if="form.errors.guardian_id_card_file" class="text-xs text-red-600">
                                        {{ form.errors.guardian_id_card_file }}
                                    </p>
                                </div>
                                <div class="grid gap-2 lg:col-span-2">
                                    <Label for="kindergarten_certificate_file">Ijazah TK/PAUD</Label>
                                    <Input
                                        id="kindergarten_certificate_file"
                                        accept="image/*,application/pdf"
                                        required
                                        type="file"
                                        @change="(e) => (form.kindergarten_certificate_file = (e.target as HTMLInputElement).files?.[0] ?? null)"
                                    />
                                    <p v-if="form.errors.kindergarten_certificate_file" class="text-xs text-red-600">
                                        {{ form.errors.kindergarten_certificate_file }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <Button
                                :disabled="form.processing"
                                class="bg-emerald-600 text-white hover:bg-emerald-700 dark:bg-emerald-400 dark:text-[#0a0a0a] dark:hover:bg-emerald-300"
                                type="submit"
                                >Kirim Pendaftaran</Button
                            >
                            <Button asChild variant="outline"><Link :href="route('home')">Batal</Link></Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </main>

        <footer id="kontak" class="border-t border-black/5 py-10 dark:border-white/10">
            <div class="mx-auto grid w-full max-w-6xl gap-8 px-6 text-sm md:grid-cols-3">
                <div class="space-y-2">
                    <p class="font-semibold text-[#1b1b18] dark:text-white">SDN HARAPAN</p>
                    <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">
                        Jln Nagrak Rt 014 Rw 003, Desa Ciwalat<br />Pabuaran, Sukabumi, Jawa Barat
                    </p>
                </div>
                <div class="space-y-2">
                    <p class="font-semibold text-[#1b1b18] dark:text-white">Tautan</p>
                    <div class="flex flex-col gap-1 text-xs">
                        <Link :href="route('home') + '#hero'">Beranda</Link>
                        <Link :href="route('home') + '#visi-misi'">Visi & Misi</Link>
                        <Link :href="route('home') + '#info-ppdb'">PPDB</Link>
                        <Link :href="route('home') + '#kontak'">Kontak</Link>
                    </div>
                </div>
            </div>
            <div class="mx-auto mt-8 flex max-w-6xl justify-between px-6 text-xs text-[#706f6c] dark:text-[#A1A09A]">
                <p>© {{ new Date().getFullYear() }} SDN HARAPAN</p>
                <p>Powered by KKN UMMI Ciwalat 2025</p>
            </div>
        </footer>
    </div>
</template>
