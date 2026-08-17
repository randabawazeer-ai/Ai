<script lang="ts">
    import { page, Link } from '@inertiajs/svelte';
    import Plus from 'lucide-svelte/icons/plus';
    import type { Snippet } from 'svelte';
    import AppContent from '@/components/AppContent.svelte';
    import AppShell from '@/components/AppShell.svelte';
    import AppSidebar from '@/components/AppSidebar.svelte';
    import AppSidebarHeader from '@/components/AppSidebarHeader.svelte';
    import { Toaster } from '@/components/ui/sonner';
    import { toUrl } from '@/lib/utils';
    import type { BreadcrumbItem } from '@/types';

    let {
        breadcrumbs = [],
        children,
    }: {
        breadcrumbs?: BreadcrumbItem[];
        children?: Snippet;
    } = $props();
</script>

<AppShell variant="sidebar">
    <AppSidebar />
    <AppContent variant="sidebar" class="overflow-x-hidden">
        <AppSidebarHeader {breadcrumbs} />
        {#key page.url}
            <div class="animate-page-in">
                {@render children?.()}
            </div>
        {/key}
    </AppContent>
    {#if !page.url.startsWith('/chat')}
        <Link
            href={toUrl('/transactions/create')}
            aria-label="إضافة معاملة"
            class="fixed bottom-6 end-6 z-50 flex size-14 items-center justify-center rounded-full bg-primary text-primary-foreground shadow-lg shadow-emerald-950/30 transition hover:bg-primary/90 active:scale-95 md:hidden"
        >
            <Plus class="size-6" />
        </Link>
    {/if}
    <Toaster />
</AppShell>
