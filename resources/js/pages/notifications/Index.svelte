<script module lang="ts">
    export const layout = {
        breadcrumbs: [{ title: 'الإشعارات', href: '/notifications' }],
    };
</script>

<script lang="ts">
    import { page, router } from '@inertiajs/svelte';
    import AppHead from '@/components/AppHead.svelte';
    import { Button } from '@/components/ui/button';
    import { Label } from '@/components/ui/label';
    import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
    import { Switch } from '@/components/ui/switch';
    import { Slider } from '@/components/ui/slider';
    import { Input } from '@/components/ui/input';
    import Bell from 'lucide-svelte/icons/bell';
    import CheckCheck from 'lucide-svelte/icons/check-check';
    import Wallet from 'lucide-svelte/icons/wallet';
    import Settings from 'lucide-svelte/icons/settings';

    const notifications = $derived(page.props.notifications as Array<{
        id: number;
        type: string;
        title: string;
        body: string;
        read_at: string | null;
        created_at: string;
    }> | null);

    const preferences = $derived(page.props.preferences as {
        expense_reminder_enabled: boolean;
        expense_reminder_time: string;
        budget_alert_enabled: boolean;
        budget_alert_threshold: number;
    });

    function markRead(id: number) {
        router.post(`/notifications/${id}/read`, {}, { preserveScroll: true });
    }

    function markAllRead() {
        router.post('/notifications/mark-all-read', {}, { preserveScroll: true });
    }

    function updatePrefs() {
        router.patch('/notifications/preferences', preferences, { preserveScroll: true });
    }
</script>

<AppHead title="الإشعارات" />

<div class="flex flex-col gap-6 p-4">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold tracking-tight">الإشعارات</h2>
        {#if notifications?.some(n => !n.read_at)}
            <Button variant="ghost" size="sm" onclick={markAllRead}>
                <CheckCheck class="size-4" />
                <span>تعليم الكل مقروء</span>
            </Button>
        {/if}
    </div>

    <Card>
        <CardHeader>
            <CardTitle class="flex items-center gap-2 text-sm">
                <Settings class="size-4" /> تفضيلات الإشعارات
            </CardTitle>
        </CardHeader>
        <CardContent class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium">تذكير المصاريف اليومي</p>
                    <p class="text-xs text-muted-foreground">تذكير لإدخال مصاريفك اليومية</p>
                </div>
                <Switch
                    checked={preferences.expense_reminder_enabled}
                    onCheckedChange={(v) => { preferences.expense_reminder_enabled = v; updatePrefs(); }}
                />
            </div>
            {#if preferences.expense_reminder_enabled}
                <div>
                    <Label class="text-xs">وقت التذكير</Label>
                    <Input
                        type="time"
                        class="mt-1 w-32"
                        bind:value={preferences.expense_reminder_time}
                        onchange={updatePrefs}
                    />
                </div>
            {/if}
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium">تنبيهات الميزانية</p>
                    <p class="text-xs text-muted-foreground">تنبيه عند الاقتراب من تجاوز الميزانية</p>
                </div>
                <Switch
                    checked={preferences.budget_alert_enabled}
                    onCheckedChange={(v) => { preferences.budget_alert_enabled = v; updatePrefs(); }}
                />
            </div>
            {#if preferences.budget_alert_enabled}
                <div>
                    <Label class="text-xs">نسبة التنبيه: {preferences.budget_alert_threshold}%</Label>
                    <div class="mt-2 flex items-center gap-2">
                        <Slider
                            value={[preferences.budget_alert_threshold]}
                            onValueChange={(v) => { preferences.budget_alert_threshold = v[0]; }}
                            max={100}
                            min={50}
                            step={5}
                            class="flex-1"
                        />
                    </div>
                    <Button size="sm" variant="outline" class="mt-1" onclick={updatePrefs}>حفظ النسبة</Button>
                </div>
            {/if}
        </CardContent>
    </Card>

    <div>
        <h3 class="mb-4 text-lg font-semibold">سجل الإشعارات</h3>
        {#if notifications?.length}
            <div class="space-y-2">
                {#each notifications as n (n.id)}
                    <Card class={n.read_at ? 'opacity-60' : 'border-primary/30'}>
                        <CardContent class="flex items-start gap-3 p-3" onclick={() => markRead(n.id)}>
                            <div class="flex size-8 shrink-0 items-center justify-center rounded-full bg-primary/10">
                                {#if n.type === 'budget_alert'}
                                    <Wallet class="size-4 text-primary" />
                                {:else}
                                    <Bell class="size-4 text-primary" />
                                {/if}
                            </div>
                            <div class="flex-1 text-right">
                                <p class="text-sm font-medium">{n.title}</p>
                                <p class="text-xs text-muted-foreground">{n.body}</p>
                                <p class="mt-1 text-xs text-muted-foreground">{n.created_at}</p>
                            </div>
                            {#if !n.read_at}
                                <div class="size-2 rounded-full bg-primary"></div>
                            {/if}
                        </CardContent>
                    </Card>
                {/each}
            </div>
        {:else}
            <div class="flex flex-col items-center justify-center rounded-xl border py-12 text-center">
                <Bell class="size-12 text-muted-foreground/50" />
                <p class="mt-4 text-muted-foreground">لا توجد إشعارات</p>
            </div>
        {/if}
    </div>
</div>
