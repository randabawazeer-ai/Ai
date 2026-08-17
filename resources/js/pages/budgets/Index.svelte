<script module lang="ts">
    export const layout = {
        breadcrumbs: [{ title: 'الميزانية', href: '/budgets' }],
    };
</script>

<script lang="ts">
    import { page, router } from '@inertiajs/svelte';
    import Plus from 'lucide-svelte/icons/plus';
    import Trash2 from 'lucide-svelte/icons/trash-2';
    import TrendingDown from 'lucide-svelte/icons/trending-down';
    import Wallet from 'lucide-svelte/icons/wallet';
    import AppHead from '@/components/AppHead.svelte';
    import { Button } from '@/components/ui/button';
    import {
        Card,
        CardContent,
        CardHeader,
        CardTitle,
    } from '@/components/ui/card';
    import {
        Dialog,
        DialogContent,
        DialogHeader,
        DialogTitle,
        DialogTrigger,
    } from '@/components/ui/dialog';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import { Progress } from '@/components/ui/progress';
    import {
        Select,
        SelectContent,
        SelectItem,
        SelectTrigger,
    } from '@/components/ui/select';

    const budgets = $derived(
        page.props.budgets as Array<{
            id: number;
            amount: number;
            month: string;
            category_id: number | null;
            category: { id: number; name: string } | null;
        }> | null,
    );

    const categories = $derived(
        page.props.categories as Array<{
            id: number;
            name: string;
        }> | null,
    );

    const month = $derived(page.props.month as string);
    const spending = $derived(page.props.spending as Record<string, number>);

    let showDialog = $state(false);
    let processing = $state(false);

    let form = $state({
        category_id: '' as string,
        amount: '',
    });

    function saveBudget() {
        processing = true;
        router.post(
            '/budgets',
            {
                category_id: form.category_id || null,
                amount: form.amount,
                month,
            },
            {
                onFinish: () => {
                    processing = false;
                    showDialog = false;
                    form = { category_id: '', amount: '' };
                },
            },
        );
    }

    function deleteBudget(id: number) {
        if (confirm('هل أنت متأكد من حذف هذه الميزانية؟')) {
            router.delete(`/budgets/${id}`, { preserveScroll: true });
        }
    }

    function changeMonth(direction: number) {
        const [y, m] = month.split('-').map(Number);
        const d = new Date(y, m - 1 + direction, 1);
        const newMonth = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}`;
        router.get(`/budgets?month=${newMonth}`, {}, { preserveState: true });
    }

    const totalBudget = $derived(
        (budgets || [])
            .filter((b) => !b.category_id)
            .reduce((sum, b) => sum + Number(b.amount), 0),
    );

    const totalSpent = $derived(
        Object.values(spending || {}).reduce((sum, v) => sum + Number(v), 0),
    );

    const monthLabel = $derived.by(() => {
        const [y, m] = month.split('-');
        const months = [
            'يناير',
            'فبراير',
            'مارس',
            'إبريل',
            'مايو',
            'يونيو',
            'يوليو',
            'أغسطس',
            'سبتمبر',
            'أكتوبر',
            'نوفمبر',
            'ديسمبر',
        ];

        return `${months[Number(m) - 1]} ${y}`;
    });

    const budgetPercent = $derived(
        totalBudget > 0 ? Math.min(100, (totalSpent / totalBudget) * 100) : 0,
    );
</script>

<AppHead title="الميزانية" />

<div class="flex flex-col gap-6 p-4">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold tracking-tight">الميزانية</h2>
            <p class="text-muted-foreground">إدارة ميزانيتك الشهرية</p>
        </div>
        <Dialog open={showDialog} onOpenChange={(v) => (showDialog = v)}>
            <DialogTrigger asChild>
                <Button onclick={() => (showDialog = true)}>
                    <Plus class="size-4" />
                    <span>إضافة ميزانية</span>
                </Button>
            </DialogTrigger>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>إضافة ميزانية</DialogTitle>
                </DialogHeader>
                <form
                    onsubmit={(e) => {
                        e.preventDefault();
                        saveBudget();
                    }}
                    class="space-y-4"
                >
                    <div>
                        <Label
                            >التصنيف (اختياري - اتركه فارغاً للميزانية
                            الإجمالية)</Label
                        >
                        <Select
                            value={form.category_id}
                            onValueChange={(v) => (form.category_id = v)}
                        >
                            <SelectTrigger class="mt-2">
                                <span
                                    class={form.category_id
                                        ? ''
                                        : 'text-muted-foreground'}
                                >
                                    {form.category_id
                                        ? categories?.find(
                                              (c) =>
                                                  String(c.id) ===
                                                  form.category_id,
                                          )?.name
                                        : 'ميزانية إجمالية'}
                                </span>
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="">ميزانية إجمالية</SelectItem
                                >
                                {#each categories || [] as cat (cat.id)}
                                    <SelectItem value={String(cat.id)}
                                        >{cat.name}</SelectItem
                                    >
                                {/each}
                            </SelectContent>
                        </Select>
                    </div>
                    <div>
                        <Label for="budget-amount">المبلغ (ر.س)</Label>
                        <Input
                            id="budget-amount"
                            type="number"
                            step="0.01"
                            min="1"
                            class="mt-2"
                            placeholder="0.00"
                            bind:value={form.amount}
                            required
                        />
                    </div>
                    <Button type="submit" disabled={processing || !form.amount}
                        >حفظ</Button
                    >
                </form>
            </DialogContent>
        </Dialog>
    </div>

    <div class="flex items-center justify-between">
        <Button variant="outline" size="sm" onclick={() => changeMonth(-1)}
            >←</Button
        >
        <span class="text-lg font-semibold">{monthLabel}</span>
        <Button variant="outline" size="sm" onclick={() => changeMonth(1)}
            >→</Button
        >
    </div>

    {#if totalBudget > 0}
        <Card>
            <CardHeader class="pb-2">
                <CardTitle class="text-sm font-medium"
                    >الميزانية الإجمالية</CardTitle
                >
            </CardHeader>
            <CardContent>
                <div class="mb-2 flex justify-between text-sm">
                    <span class="text-muted-foreground"
                        >تم صرف {totalSpent.toLocaleString('ar-SA')} ر.س</span
                    >
                    <span class="font-semibold" dir="ltr"
                        >{totalBudget.toLocaleString('ar-SA')} ر.س</span
                    >
                </div>
                <Progress
                    value={budgetPercent}
                    class={budgetPercent >= 100
                        ? 'h-2 bg-destructive'
                        : 'h-2 bg-primary'}
                />
                <p class="mt-1 text-xs text-muted-foreground">
                    {#if budgetPercent >= 100}
                        <span class="text-expense">تم تجاوز الميزانية!</span>
                    {:else if budgetPercent >= 80}
                        <span class="text-amber-500"
                            >تنبيه: تم صرف {budgetPercent.toFixed(0)}% من
                            الميزانية</span
                        >
                    {:else}
                        متبقي {(totalBudget - totalSpent).toLocaleString(
                            'ar-SA',
                        )} ر.س
                    {/if}
                </p>
            </CardContent>
        </Card>
    {/if}

    <div>
        <h3 class="mb-4 text-lg font-semibold">تفاصيل الميزانية</h3>
        {#if budgets?.length}
            <div class="space-y-3">
                {#each budgets as b (b.id)}
                    {@const spent = Number(
                        spending?.[String(b.category_id)] || 0,
                    )}
                    {@const pct =
                        Number(b.amount) > 0
                            ? Math.min(100, (spent / Number(b.amount)) * 100)
                            : 0}
                    <Card>
                        <CardContent class="flex items-center gap-4 p-4">
                            <div
                                class="flex size-10 items-center justify-center rounded-full bg-expense/10 text-expense"
                            >
                                <TrendingDown class="size-5" />
                            </div>
                            <div class="flex-1 text-right">
                                <p class="font-medium">
                                    {b.category?.name || 'ميزانية إجمالية'}
                                </p>
                                <div class="mt-1">
                                    <Progress
                                        value={pct}
                                        class={pct >= 100
                                            ? 'h-1.5 bg-expense'
                                            : 'h-1.5 bg-primary'}
                                    />
                                </div>
                                <p
                                    class="mt-1 text-xs text-muted-foreground"
                                    dir="ltr"
                                >
                                    {spent.toLocaleString('ar-SA')} / {Number(
                                        b.amount,
                                    ).toLocaleString('ar-SA')} ر.س
                                    {#if pct >= 100}
                                        <span class="text-expense">
                                            ⚠ تجاوز</span
                                        >
                                    {:else}
                                        ({pct.toFixed(0)}%)
                                    {/if}
                                </p>
                            </div>
                            <Button
                                variant="ghost"
                                size="icon"
                                onclick={() => deleteBudget(b.id)}
                            >
                                <Trash2 class="size-4 text-destructive" />
                            </Button>
                        </CardContent>
                    </Card>
                {/each}
            </div>
        {:else}
            <div
                class="flex flex-col items-center justify-center rounded-xl border py-12 text-center"
            >
                <Wallet class="size-12 text-muted-foreground/50" />
                <p class="mt-4 text-muted-foreground">
                    لا توجد ميزانيات محددة لهذا الشهر
                </p>
                <Button
                    variant="outline"
                    class="mt-4"
                    onclick={() => (showDialog = true)}>أضف ميزانية</Button
                >
            </div>
        {/if}
    </div>
</div>
