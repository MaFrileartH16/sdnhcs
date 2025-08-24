<script lang="ts" setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import AppLogo from './AppLogo.vue';

import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';

// Ikon
import { ClipboardCheck, ClipboardList, LayoutDashboard, Users } from 'lucide-vue-next';

// Dapatkan peran user dari props Inertia
const page = usePage();
const role = computed(() => (page.props as any)?.auth?.user?.role ?? 'guardian');

// Buat nav utama berdasarkan peran
const mainNavItems = computed<NavItem[]>(() =>
    role.value === 'admin'
        ? [
              { title: 'Beranda', href: route('admin.dashboard'), icon: LayoutDashboard },
              { title: 'Pendaftar', href: route('admin.applicants.index'), icon: Users },
              { title: 'Tes', href: route('admin.tests.index'), icon: ClipboardCheck },
          ]
        : [
              { title: 'Beranda', href: route('guardian.dashboard'), icon: LayoutDashboard },
              { title: 'Pendaftaran Saya', href: route('guardian.applicants.index'), icon: ClipboardList },
          ],
);

// Link logo diarahkan ke dashboard sesuai peran
const homeHref = computed(() => route(role.value === 'admin' ? 'admin.dashboard' : 'guardian.dashboard'));

// (opsional) menu footer
const footerNavItems: NavItem[] = [];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton as-child size="lg">
                        <Link :href="homeHref">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>

    <slot />
</template>
