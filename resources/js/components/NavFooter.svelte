<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import {
        SidebarGroup,
        SidebarGroupContent,
        SidebarMenu,
        SidebarMenuButton,
        SidebarMenuItem,
    } from '@/components/ui/sidebar';
    import { toUrl } from '@/lib/utils';
    import type { NavItem } from '@/types';

    let {
        items = [],
        class: className = '',
    }: {
        items: NavItem[];
        class?: string;
    } = $props();
</script>

<SidebarGroup class={`group-data-[collapsible=icon]:p-0 ${className}`}>
    <SidebarGroupContent>
        <SidebarMenu>
            {#each items as item (toUrl(item.href))}
                <SidebarMenuItem>
                    <SidebarMenuButton
                        class="text-muted-foreground hover:text-foreground h-10 gap-3"
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
    </SidebarGroupContent>
</SidebarGroup>
