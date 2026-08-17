<script module lang="ts">
    export const layout = {
        breadcrumbs: [{ title: 'لوحة التحكم', href: '/dashboard' }],
    };
</script>

<script lang="ts">
    import { Link, page } from '@inertiajs/svelte';
    import ArrowDownRight from 'lucide-svelte/icons/arrow-down-right';
    import ArrowUpRight from 'lucide-svelte/icons/arrow-up-right';
    import MessageCircle from 'lucide-svelte/icons/message-circle';
    import Plus from 'lucide-svelte/icons/plus';
    import TrendingDown from 'lucide-svelte/icons/trending-down';
    import TrendingUp from 'lucide-svelte/icons/trending-up';
    import Wallet from 'lucide-svelte/icons/wallet';
    import AppHead from '@/components/AppHead.svelte';
    import { Button } from '@/components/ui/button';
    import {
        Card,
        CardContent,
        CardHeader,
        CardTitle,
    } from '@/components/ui/card';
    import { Progress } from '@/components/ui/progress';
    import { toUrl } from '@/lib/utils';

    const monthLabels = [
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

    const stats = $derived(
        page.props.stats as {
            total_expenses: number;
            total_income: number;
            balance: number | null;
            total_budget: number;
            budget_percent: number;
        } | null,
    );

    const recentTransactions = $derived(
        page.props.recentTransactions as Array<{
            id: number;
            type: string;
            amount: number;
            description: string;
            transaction_date: string;
            category: { name: string; icon: string } | null;
        }> | null,
    );

    const monthlyExpenses = $derived(
        page.props.monthlyExpenses as number[] | null,
    );

    const currentMonthIndex = $derived(new Date().getMonth());

    const maxExpense = $derived(
        monthlyExpenses?.length ? Math.max(...monthlyExpenses, 1) : 1,
    );

    const chartBars = $derived(
        monthlyExpenses?.map((value, index) => {
            const monthIndex = (currentMonthIndex - (5 - index) + 12) % 12;

            return {
                value,
                label: monthLabels[monthIndex].slice(0, 3),
                height: Math.round((value / maxExpense) * 120),
            };
        }) ?? [],
    );
</script>

<AppHead title="لوحة التحكم" />

<div class="flex flex-col gap-6 p-4">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold tracking-tight">لوحة التحكم</h2>
            <p class="text-muted-foreground">
                مرحباً بك في مدبّر، هذا ملخص مصاريفك
            </p>
        </div>
        <div class="flex items-center gap-2">
            <Button variant="outline" asChild>
                <Link href={toUrl('/assistant')}>
                    <MessageCircle class="size-4" />
                    <span>اسأل المساعد</span>
                </Link>
            </Button>
            <Button asChild>
                <Link href={toUrl('/transactions/create')}>
                    <Plus class="size-4" />
                    <span>إضافة معاملة</span>
                </Link>
            </Button>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        <Card>
            <CardHeader
                class="flex flex-row items-center justify-between space-y-0 pb-2"
            >
                <CardTitle class="text-sm font-medium"
                    >إجمالي المصاريف</CardTitle
                >
                <div
                    class="flex size-8 items-center justify-center rounded-full bg-expense/10 text-expense"
                >
                    <TrendingDown class="size-4" />
                </div>
            </CardHeader>
            <CardContent>
                <div class="text-2xl font-bold" dir="ltr">
                    {stats?.total_expenses?.toLocaleString('ar-SA') ?? '0'} ر.س
                </div>
            </CardContent>
        </Card>

        <Card>
            <CardHeader
                class="flex flex-row items-center justify-between space-y-0 pb-2"
            >
                <CardTitle class="text-sm font-medium"
                    >إجمالي الإيرادات</CardTitle
                >
                <div
                    class="flex size-8 items-center justify-center rounded-full bg-income/10 text-income"
                >
                    <TrendingUp class="size-4" />
                </div>
            </CardHeader>
            <CardContent>
                <div class="text-2xl font-bold" dir="ltr">
                    {stats?.total_income?.toLocaleString('ar-SA') ?? '0'} ر.س
                </div>
            </CardContent>
        </Card>

        <Card>
            <CardHeader
                class="flex flex-row items-center justify-between space-y-0 pb-2"
            >
                <CardTitle class="text-sm font-medium"
                    >المتبقي من الميزانية</CardTitle
                >
                <div
                    class="flex size-8 items-center justify-center rounded-full bg-primary/10 text-primary"
                >
                    <Wallet class="size-4" />
                </div>
            </CardHeader>
            <CardContent>
                <div class="text-2xl font-bold" dir="ltr">
                    {stats?.balance != null
                        ? stats.balance.toLocaleString('ar-SA') + ' ر.س'
                        : 'لم تحدد ميزانية'}
                </div>
            </CardContent>
        </Card>
    </div>

    {#if stats?.total_budget > 0}
        <Card
            class="border-primary/20 bg-primary/5 dark:border-primary/30 dark:bg-primary/10"
        >
            <CardContent class="flex items-center gap-4 p-4">
                <div
                    class="flex size-12 items-center justify-center rounded-full bg-primary/10 text-primary"
                >
                    <Wallet class="size-6" />
                </div>
                <div class="flex-1 text-right">
                    <p class="text-sm text-muted-foreground">
                        الميزانية الشهرية
                    </p>
                    <div class="mt-1">
                        <Progress
                            value={stats.budget_percent}
                            class="h-2 {stats.budget_percent >= 100
                                ? '[&_[data-slot=progress-indicator]]:bg-expense'
                                : ''}"
                        />
                    </div>
                    <p class="mt-1 text-xs text-muted-foreground" dir="ltr">
                        تم صرف {stats.total_expenses.toLocaleString('ar-SA')} من {stats.total_budget.toLocaleString(
                            'ar-SA',
                        )} ر.س
                        {#if stats.budget_percent >= 100}
                            <span class="text-expense font-semibold">
                                ⚠ تجاوزت الميزانية!</span
                            >
                        {:else if stats.budget_percent >= 80}
                            <span class="text-amber-500">
                                ({stats.budget_percent.toFixed(0)}%)</span
                            >
                        {:else}
                            <span> ({stats.budget_percent.toFixed(0)}%)</span>
                        {/if}
                    </p>
                </div>
            </CardContent>
        </Card>
    {/if}

    <Card>
        <CardHeader>
            <CardTitle class="text-sm font-medium"
                >المصاريف - آخر 6 أشهر</CardTitle
            >
        </CardHeader>
        <CardContent>
            {#if monthlyExpenses?.some((value) => value > 0)}
                <div
                    class="flex h-40 items-end justify-between gap-2"
                    dir="ltr"
                >
                    {#each chartBars as bar, index (index)}
                        <div class="flex flex-1 flex-col items-center gap-1">
                            <div
                                class="w-4 rounded-t bg-expense"
                                style="height:{bar.height}px"
                                title={`{bar.value.toLocaleString('ar-SA')} ر.س`}
                            ></div>
                            <span class="text-xs text-muted-foreground"
                                >{bar.label}</span
                            >
                        </div>
                    {/each}
                </div>
            {:else}
                <p class="py-8 text-center text-muted-foreground">
                    لا توجد بيانات بعد
                </p>
            {/if}
        </CardContent>
    </Card>

    <div>
        <h3 class="mb-4 text-lg font-semibold">آخر المعاملات</h3>
        {#if recentTransactions?.length}
            <div class="rounded-xl border">
                {#each recentTransactions as tx (tx.id)}
                    <div
                        class="flex items-center gap-4 border-b p-4 last:border-0"
                    >
                        <div
                            class="flex size-10 items-center justify-center rounded-full {tx.type ===
                            'income'
                                ? 'bg-income/10 text-income'
                                : 'bg-expense/10 text-expense'}"
                        >
                            {#if tx.type === 'income'}
                                <ArrowUpRight class="size-5" />
                            {:else}
                                <ArrowDownRight class="size-5" />
                            {/if}
                        </div>
                        <div class="flex-1 text-right">
                            <p class="font-medium">
                                {tx.description || 'بدون وصف'}
                            </p>
                            <p class="text-sm text-muted-foreground">
                                {tx.category?.name || 'بدون تصنيف'} · {tx.transaction_date}
                            </p>
                        </div>
                        <div
                            class="font-semibold {tx.type === 'income'
                                ? 'text-income'
                                : 'text-expense'}"
                            dir="ltr"
                        >
                            {tx.type === 'income'
                                ? '+'
                                : '-'}{tx.amount?.toLocaleString('ar-SA')} ر.س
                        </div>
                    </div>
                {/each}
            </div>
        {:else}
            <div
                class="flex flex-col items-center justify-center rounded-xl border py-12 text-center"
            >
                <Wallet class="size-12 text-muted-foreground/50" />
                <p class="mt-4 text-muted-foreground">لا توجد معاملات بعد</p>
                <Button variant="outline" class="mt-4" asChild>
                    <Link href={toUrl('/transactions/create')}
                        >أضف أول معاملة</Link
                    >
                </Button>
            </div>
        {/if}
    </div>
</div>
