<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    BarChart3,
    BookOpen,
    Building2,
    CreditCard,
    FileText,
    FolderGit2,
    LayoutGrid,
    Package,
    Receipt,
    Settings2,
    Users,
} from '@lucide/vue';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { index as customersIndex } from '@/routes/customers';
import type { NavItem } from '@/types';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Master Data',
        icon: Package,
        items: [
            { title: 'Customers', href: customersIndex(), icon: Users },
            { title: 'Products', href: '#', icon: Package },
        ],
    },
    {
        title: 'Transactions',
        icon: Receipt,
        items: [
            { title: 'Invoices', href: '#', icon: FileText },
            { title: 'Payments', href: '#', icon: CreditCard },
        ],
    },
    {
        title: 'Reports',
        icon: BarChart3,
        items: [
            { title: 'Revenue Report', href: '#' },
            { title: 'Invoice Report', href: '#' },
        ],
    },
    {
        title: 'Settings',
        icon: Settings2,
        items: [
            { title: 'Company Profile', href: '#', icon: Building2 },
            { title: 'Invoice Settings', href: '#', icon: Settings2 },
        ],
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: FolderGit2,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
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
