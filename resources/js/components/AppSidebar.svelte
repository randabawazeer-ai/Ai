<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import ArrowRightLeft from 'lucide-svelte/icons/arrow-right-left';
    import Bell from 'lucide-svelte/icons/bell';
    import ChartNoAxesCombined from 'lucide-svelte/icons/chart-no-axes-combined';
    import LayoutGrid from 'lucide-svelte/icons/layout-grid';
    import MessageCircle from 'lucide-svelte/icons/message-circle';
    import Users from 'lucide-svelte/icons/users';
    import Settings from 'lucide-svelte/icons/settings';
    import Tags from 'lucide-svelte/icons/tags';
    import type { Snippet } from 'svelte';
    import AppLogo from '@/components/AppLogo.svelte';
    import NavFooter from '@/components/NavFooter.svelte';
    import NavMain from '@/components/NavMain.svelte';
    import NavUser from '@/components/NavUser.svelte';
    import {
        Sidebar,
        SidebarContent,
        SidebarFooter,
        SidebarHeader,
        SidebarMenu,
        SidebarMenuButton,
        SidebarMenuItem,
    } from '@/components/ui/sidebar';
    import { toUrl } from '@/lib/utils';
    import { dashboard } from '@/routes';
    import { edit } from '@/routes/profile';
    import type { NavItem } from '@/types';

    let {
        children,
    }: {
        children?: Snippet;
    } = $props();

    const mainNavItems: NavItem[] = [
        {
            title: 'لوحة التحكم',
            href: dashboard(),
            icon: LayoutGrid,
        },
        {
            title: 'المعاملات',
            href: '/transactions',
            icon: ArrowRightLeft,
        },
        {
            title: 'الميزانية',
            href: '/budgets',
            icon: ChartNoAxesCombined,
        },
        {
            title: 'العائلة',
            href: '/family',
            icon: Users,
        },
        {
            title: 'المساعد الذكي',
            href: '/assistant',
            icon: MessageCircle,
        },
        {
            title: 'التصنيفات',
            href: '/categories',
            icon: Tags,
        },
    ];

    const footerNavItems: NavItem[] = [
        {
            title: 'الإشعارات',
            href: '/notifications',
            icon: Bell,
        },
        {
            title: 'الإعدادات',
            href: edit(),
            icon: Settings,
        },
    ];
</script>

<Sidebar collapsible="offcanvas" side="left">
    <SidebarHeader class="gap-3 p-4">
        <SidebarMenu>
            <SidebarMenuItem>
                <SidebarMenuButton
                    size="lg"
                    class="h-14 overflow-visible [&>span:last-child]:overflow-visible [&>span:last-child]:text-clip [&_svg]:size-6"
                >
                    {#snippet child({ props })}
                        <Link
                            {...props}
                            href={toUrl(dashboard())}
                            class={props?.class}
                        >
                            <AppLogo />
                        </Link>
                    {/snippet}
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarHeader>

    <SidebarContent class="gap-2 px-2">
        <NavMain items={mainNavItems} />
    </SidebarContent>

    <SidebarFooter class="gap-1 border-t border-sidebar-border p-2">
        <NavFooter items={footerNavItems} />
        <NavUser />
    </SidebarFooter>
</Sidebar>
{@render children?.()}
