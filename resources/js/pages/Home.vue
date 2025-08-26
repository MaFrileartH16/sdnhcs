<script lang="ts" setup>
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { Sheet, SheetContent, SheetTrigger } from '@/components/ui/sheet';
import { Head, Link } from '@inertiajs/vue3';
import { BookOpen, CheckCircle2, ClipboardList, FileCheck2 } from 'lucide-vue-next';
import { ref } from 'vue';

const nav = ref([
    { label: 'Beranda', href: '#hero' },
    { label: 'Visi & Misi', href: '#visi-misi' },
    { label: 'PPDB', href: '#info-ppdb' },
    { label: 'Kontak', href: '#kontak' },
]);

const heroCtas = ref([
    { label: 'Daftar PPDB', href: route('admissions.index'), variant: 'primary' },
    { label: 'Masuk Akun', href: route('login'), variant: 'outline' },
]);

const mission = ref([
    'Menciptakan suasana pembelajaran yang efektif, kreatif, dan inovatif.',
    'Mewujudkan lingkungan akademis yang sehat, kondusif, dan harmonis.',
    'Membudayakan tatakrama Islami dan akhlakul karimah.',
]);

const steps = ref([
    { icon: ClipboardList, text: 'Isi formulir daring & unggah berkas.' },
    { icon: BookOpen, text: 'Mengerjakan tes online melalui tautan eksternal setelah mengirim formulir.' },
    { icon: CheckCircle2, text: 'Pengumuman hasil seleksi pada halaman akun/portal.' },
]);

const requirements = ref(['Formulir Pendaftaran', 'Kartu Keluarga', 'Akta Lahir', 'KTP Orang Tua/Wali', 'Ijazah TK/PAUD']);
</script>

<template>
    <Head title="Beranda" />
    <div
        class="min-h-screen bg-gradient-to-b from-emerald-50 via-yellow-50 to-white text-[#1b1b18] dark:from-emerald-900/30 dark:via-yellow-900/20 dark:to-[#0a0a0a] dark:text-[#EDEDEC]"
    >
        <header class="sticky top-0 z-40 border-b border-black/5 bg-white/70 backdrop-blur-md dark:border-white/10 dark:bg-[#0a0a0a]/70">
            <div class="mx-auto flex w-full max-w-6xl items-center justify-between px-6 py-3">
                <Link :href="route('home') + '#hero'" aria-label="Beranda" class="flex items-center">
                    <img alt="Logo Sekolah" class="h-10 w-10 object-contain" src="/logo.png" />
                </Link>

                <nav class="hidden items-center gap-1 text-sm md:flex">
                    <Button v-for="n in nav" :key="n.href" asChild class="px-3 hover:bg-emerald-50 dark:hover:bg-emerald-900/30" variant="ghost">
                        <a :href="n.href">{{ n.label }}</a>
                    </Button>
                </nav>
                <div class="hidden items-center gap-2 md:flex">
                    <Button
                        v-for="c in heroCtas"
                        :key="c.label"
                        :class="
                            c.variant === 'outline'
                                ? 'hover:border-emerald-600 hover:text-emerald-700 dark:hover:text-emerald-300'
                                : 'bg-emerald-600 text-white shadow-sm hover:bg-emerald-700 dark:bg-emerald-400 dark:text-[#0a0a0a] dark:hover:bg-emerald-300'
                        "
                        :variant="c.variant === 'outline' ? 'outline' : undefined"
                        asChild
                    >
                        <a :href="c.href">{{ c.label }}</a>
                    </Button>
                </div>
                <div class="md:hidden">
                    <Sheet>
                        <SheetTrigger as-child>
                            <Button size="sm" variant="outline">Menu</Button>
                        </SheetTrigger>
                        <SheetContent class="w-72" side="right">
                            <div class="grid gap-4 py-6">
                                <div class="px-1 text-xs tracking-wide text-[#706f6c] uppercase dark:text-[#A1A09A]">Navigasi</div>
                                <Button v-for="n in nav" :key="n.href" asChild class="justify-start" variant="ghost"
                                    ><a :href="n.href">{{ n.label }}</a></Button
                                >
                                <Separator class="my-2" />
                                <div class="px-1 text-xs tracking-wide text-[#706f6c] uppercase dark:text-[#A1A09A]">Aksi</div>
                                <Button
                                    v-for="c in heroCtas"
                                    :key="c.label + '-mobile'"
                                    :class="
                                        c.variant === 'outline'
                                            ? 'w-full'
                                            : 'w-full bg-emerald-600 text-white hover:bg-emerald-700 dark:bg-emerald-400 dark:text-[#0a0a0a] dark:hover:bg-emerald-300'
                                    "
                                    :variant="c.variant === 'outline' ? 'outline' : undefined"
                                    asChild
                                >
                                    <a :href="c.href">{{ c.label }}</a>
                                </Button>
                            </div>
                        </SheetContent>
                    </Sheet>
                </div>
            </div>
        </header>

        <main class="mx-auto w-full max-w-6xl px-6 py-10">
            <section
                id="hero"
                class="relative overflow-hidden rounded-3xl border border-black/5 bg-gradient-to-r from-emerald-50 via-white to-yellow-50 shadow-sm dark:border-white/10 dark:from-emerald-900/20 dark:via-[#0a0a0a] dark:to-yellow-900/10"
            >
                <div class="relative mx-auto grid max-w-6xl items-center gap-10 px-6 py-16 md:grid-cols-2">
                    <div class="space-y-5">
                        <h1 class="text-3xl leading-tight font-bold sm:text-4xl lg:text-5xl">
                            SDN HARAPAN<br />
                            <span
                                class="bg-gradient-to-r from-emerald-700 to-yellow-500 bg-clip-text text-transparent dark:from-emerald-300 dark:to-yellow-300"
                                >Mencetak Generasi Berprestasi</span
                            >
                        </h1>
                        <p class="max-w-prose text-sm text-[#3b3b38] dark:text-[#D2D2CF]">
                            Selamat datang di website resmi SDN Harapan. Silakan gunakan navigasi untuk akses PPDB, informasi sekolah, dan kontak.
                        </p>
                        <div class="flex flex-wrap gap-3 pt-2">
                            <Button
                                v-for="c in heroCtas"
                                :key="c.label + '-hero'"
                                :class="
                                    c.variant === 'outline'
                                        ? 'border-emerald-600 text-emerald-700 hover:bg-emerald-50 dark:text-emerald-300 dark:hover:bg-emerald-900/30'
                                        : 'bg-emerald-600 text-white shadow-sm hover:scale-[1.02] hover:bg-emerald-700 dark:bg-emerald-400 dark:text-[#0a0a0a] dark:hover:bg-emerald-300'
                                "
                                :variant="c.variant === 'outline' ? 'outline' : undefined"
                                asChild
                            >
                                <a :href="c.href">{{ c.label }}</a>
                            </Button>
                        </div>
                    </div>
                    <Card class="border-none bg-white/70 backdrop-blur dark:bg-white/5">
                        <CardContent class="p-3">
                            <img
                                alt="Foto SDN Harapan"
                                class="rounded-2xl border border-black/5 shadow-md dark:border-white/10"
                                src="https://images.unsplash.com/photo-1588072432836-e10032774350?q=80&w=1172&auto=format&fit=crop&ixlib=rb-4.1.0"
                            />
                        </CardContent>
                    </Card>
                </div>
            </section>

            <section id="visi-misi" class="scroll-mt-24 py-14">
                <div class="mb-8 text-center">
                    <h2 class="text-2xl font-semibold">Visi &amp; Misi</h2>
                    <Separator class="mx-auto mt-3 w-24 bg-yellow-400/70 dark:bg-yellow-300/60" />
                </div>
                <div class="grid gap-6 lg:grid-cols-3">
                    <Card class="transition hover:shadow-md lg:col-span-1">
                        <CardHeader>
                            <CardTitle class="text-lg">Visi</CardTitle>
                            <CardDescription>Ringkas & jelas</CardDescription>
                        </CardHeader>
                        <CardContent class="text-sm leading-relaxed">Handal, Rajin, Pandai, Kreatif, dan Taqwa.</CardContent>
                    </Card>
                    <Card class="transition hover:shadow-md lg:col-span-2">
                        <CardHeader>
                            <CardTitle class="text-lg">Misi</CardTitle>
                            <CardDescription>Fokus pembinaan</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <ul class="space-y-3 text-sm leading-relaxed">
                                <li v-for="m in mission" :key="m" class="flex gap-2">
                                    <FileCheck2 class="h-4 w-4 text-emerald-700 dark:text-emerald-400" />
                                    {{ m }}
                                </li>
                            </ul>
                        </CardContent>
                    </Card>
                </div>
            </section>

            <section id="info-ppdb" class="scroll-mt-24 py-6">
                <div class="mb-8 text-center">
                    <h2 class="text-2xl font-semibold">Info PPDB</h2>
                    <Separator class="mx-auto mt-3 w-24 bg-emerald-500/70 dark:bg-emerald-400/70" />
                </div>
                <div class="grid gap-8 lg:grid-cols-2">
                    <Card class="transition hover:shadow-md">
                        <CardHeader>
                            <CardTitle>Alur Pendaftaran</CardTitle>
                            <CardDescription>Langkah ringkas tanpa jadwal</CardDescription>
                        </CardHeader>
                        <CardContent class="space-y-3 text-sm">
                            <div v-for="s in steps" :key="s.text" class="flex items-start gap-3">
                                <component :is="s.icon" class="mt-0.5 h-4 w-4 text-emerald-700 dark:text-emerald-400" />
                                {{ s.text }}
                            </div>
                        </CardContent>
                    </Card>
                    <Card class="transition hover:shadow-md">
                        <CardHeader>
                            <CardTitle>Syarat Pendaftaran</CardTitle>
                            <CardDescription>Dokumen yang wajib disiapkan</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <ul class="list-disc space-y-2 pl-5 text-sm">
                                <li v-for="r in requirements" :key="r">{{ r }}</li>
                            </ul>
                        </CardContent>
                    </Card>
                </div>
            </section>
        </main>

        <footer id="kontak" class="scroll-mt-24 border-t border-black/5 py-10 dark:border-white/10">
            <div class="mx-auto grid w-full max-w-6xl gap-8 px-6 text-sm md:grid-cols-3">
                <div class="space-y-2">
                    <p class="font-semibold text-[#1b1b18] dark:text-white">SDN HARAPAN</p>
                    <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">
                        Jln Nagrak Rt 014 Rw 003, Desa Ciwalat<br />Pabuaran, Sukabumi, Jawa Barat
                    </p>
                </div>
                <div class="space-y-2">
                    <p class="font-semibold text-[#1b1b18] dark:text-white">Kontak</p>
                    <div class="flex flex-col gap-1 text-xs">
                        <p class="text-[#706f6c] dark:text-[#A1A09A]">
                            <a class="hover:text-emerald-600 dark:hover:text-emerald-400" href="tel:+6283180073287"> +62 831-8007-3287 (Pak Budi) </a>
                        </p>
                    </div>
                </div>
                <div class="space-y-2">
                    <p class="font-semibold text-[#1b1b18] dark:text-white">Tautan</p>
                    <div class="flex flex-col gap-1 text-xs">
                        <a href="#hero">Beranda</a>
                        <a href="#visi-misi">Visi & Misi</a>
                        <a href="#info-ppdb">PPDB</a>
                        <a href="#kontak">Kontak</a>
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

<style>
html {
    scroll-behavior: smooth;
}
:target {
    scroll-margin-top: 88px;
}
</style>
