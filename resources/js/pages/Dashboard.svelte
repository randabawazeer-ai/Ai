<script module lang="ts">
    export const layout = {
        breadcrumbs: [
            { title: 'لوحة التحكم', href: '/dashboard' },
        ],
    };
</script>

<script lang="ts">
    import { Link, page } from '@inertiajs/svelte';
    import AppHead from '@/components/AppHead.svelte';
    import { toUrl } from '@/lib/utils';
    import ArrowDownRight from 'lucide-svelte/icons/arrow-down-right';
    import ArrowUpRight from 'lucide-svelte/icons/arrow-up-right';
    import Wallet from 'lucide-svelte/icons/wallet';
    import TrendingDown from 'lucide-svelte/icons/trending-down';
    import TrendingUp from 'lucide-svelte/icons/trending-up';
    import Plus from 'lucide-svelte/icons/plus';
    import { Button } from '@/components/ui/button';
    import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

    const stats = $derived(page.props.stats as {
        total_expenses: number;
        total_income: number;
        balance: number;
        transaction_count: number;
    } | null);

    const recentTransactions = $derived(page.props.recentTransactions as Array<{
        id: number;
        type: string;
        amount: number;
        description: string;
        transaction_date: string;
        category: { name: string; icon: string } | null;
    }> | null);
</script>

<AppHead title="لوحة التحكم" />

<div class="flex flex-col gap-6 p-4">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold tracking-tight">لوحة التحكم</h2>
            <p class="text-muted-foreground">مرحباً بك في مدبّر، هذا ملخص مصاريفك</p>
        </div>
        <Button asChild>
            <Link href={toUrl('/transactions/create')}>
                <Plus class="size-4" />
                <span>إضافة معاملة</span>
            </Link>
        </Button>
    </div>

    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        <Card>
            <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                <CardTitle class="text-sm font-medium">إجمالي المصاريف</CardTitle>
                <TrendingDown class="size-4 text-red-500" />
            </CardHeader>
            <CardContent>
                <div class="text-2xl font-bold" dir="ltr">
                    {stats?.total_expenses?.toLocaleString('ar-SA') ?? '0'} ر.س
                </div>
            </CardContent>
        </Card>

        <Card>
            <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                <CardTitle class="text-sm font-medium">إجمالي الإيرادات</CardTitle>
                <TrendingUp class="size-4 text-green-500" />
            </CardHeader>
            <CardContent>
                <div class="text-2xl font-bold" dir="ltr">
                    {stats?.total_income?.toLocaleString('ar-SA') ?? '0'} ر.س
                </div>
            </CardContent>
        </Card>

        <Card>
            <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                <CardTitle class="text-sm font-medium">الرصيد</CardTitle>
                <Wallet class="size-4 text-blue-500" />
            </CardHeader>
            <CardContent>
                <div class="text-2xl font-bold" dir="ltr">
                    {stats?.balance?.toLocaleString('ar-SA') ?? '0'} ر.س
                </div>
            </CardContent>
        </Card>

        <Card>
            <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                <CardTitle class="text-sm font-medium">عدد المعاملات</CardTitle>
                <Wallet class="size-4 text-muted-foreground" />
            </CardHeader>
            <CardContent>
                <div class="text-2xl font-bold" dir="ltr">
                    {stats?.transaction_count ?? '0'}
                </div>
            </CardContent>
        </Card>
    </div>

    <div>
        <h3 class="mb-4 text-lg font-semibold">آخر المعاملات</h3>
        {#if recentTransactions?.length}
            <div class="rounded-xl border">
                {#each recentTransactions as tx}
                    <div class="flex items-center gap-4 border-b p-4 last:border-0">
                        <div class="flex size-10 items-center justify-center rounded-full {tx.type === 'income' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600'}">
                            {#if tx.type === 'income'}
                                <ArrowUpRight class="size-5" />
                            {:else}
                                <ArrowDownRight class="size-5" />
                            {/if}
                        </div>
                        <div class="flex-1 text-right">
                            <p class="font-medium">{tx.description || 'بدون وصف'}</p>
                            <p class="text-sm text-muted-foreground">
                                {tx.category?.name || 'بدون تصنيف'} · {tx.transaction_date}
                            </p>
                        </div>
                        <div class="font-semibold {tx.type === 'income' ? 'text-green-600' : 'text-red-600'}" dir="ltr">
                            {tx.type === 'income' ? '+' : '-'}{tx.amount?.toLocaleString('ar-SA')} ر.س
                        </div>
                    </div>
                {/each}
            </div>
        {:else}
            <div class="flex flex-col items-center justify-center rounded-xl border py-12 text-center">
                <Wallet class="size-12 text-muted-foreground/50" />
                <p class="mt-4 text-muted-foreground">لا توجد معاملات بعد</p>
                <Button variant="outline" class="mt-4" asChild>
                    <Link href={toUrl('/transactions/create')}>أضف أول معاملة</Link>
                </Button>
            </div>
        {/if}
    </div>
</div>
