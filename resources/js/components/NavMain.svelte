<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import {
        SidebarGroup,
        SidebarGroupLabel,
        SidebarMenu,
        SidebarMenuButton,
        SidebarMenuItem,
    } from '@/components/ui/sidebar';
    import { currentUrlState } from '@/lib/currentUrl.svelte';
    import { toUrl } from '@/lib/utils';
    import type { NavItem } from '@/types';

    let {
        items = [],
    }: {
        items: NavItem[];
    } = $props();

    const url = currentUrlState();
</script>

<SidebarGroup class="px-3 py-2">
    <SidebarGroupLabel class="mb-2 text-xs font-medium text-muted-foreground"
        >القائمة</SidebarGroupLabel
    >
    <SidebarMenu>
        {#each items as item (toUrl(item.href))}
            <SidebarMenuItem>
                <SidebarMenuButton
                    isActive={url.isCurrentUrl(item.href, url.currentUrl)}
                    tooltip={item.title}
                    class="gap-3 rounded-lg text-sm font-normal text-muted-foreground hover:text-foreground data-[active=true]:bg-sidebar-accent data-[active=true]:text-primary data-[active=true]:font-semibold data-[active=true]:shadow-sm h-10 flex-row items-center"
                >
                    {#snippet child({ props })}
                        <Link
                            {...props}
                            href={toUrl(item.href)}
                            class={props?.class}
                        >
                            {#if item.icon}
                                <item.icon class="size-5 shrink-0" />
                            {/if}
                            <span>{item.title}</span>
                        </Link>
                    {/snippet}
                </SidebarMenuButton>
            </SidebarMenuItem>
        {/each}
    </SidebarMenu>
</SidebarGroup>
