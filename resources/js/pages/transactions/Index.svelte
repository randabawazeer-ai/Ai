<script module lang="ts">
    export const layout = {
        breadcrumbs: [{ title: 'المعاملات', href: '/transactions' }],
    };
</script>

<script lang="ts">
    import { Link, page, router } from '@inertiajs/svelte';
    import ArrowDownRight from 'lucide-svelte/icons/arrow-down-right';
    import ArrowUpRight from 'lucide-svelte/icons/arrow-up-right';
    import Filter from 'lucide-svelte/icons/filter';
    import Pencil from 'lucide-svelte/icons/pencil';
    import Plus from 'lucide-svelte/icons/plus';
    import Search from 'lucide-svelte/icons/search';
    import Trash2 from 'lucide-svelte/icons/trash-2';
    import AppHead from '@/components/AppHead.svelte';
    import { Button } from '@/components/ui/button';
    import { Card, CardContent } from '@/components/ui/card';
    import { Input } from '@/components/ui/input';
    import {
        Select,
        SelectContent,
        SelectItem,
        SelectTrigger,
    } from '@/components/ui/select';
    import { toUrl } from '@/lib/utils';

    const transactions = $derived(
        page.props.transactions as {
            data: Array<{
                id: number;
                type: string;
                amount: number;
                description: string;
                payment_method: string;
                transaction_date: string;
                category: { name: string; icon: string } | null;
            }>;
            current_page: number;
            last_page: number;
            from: number;
            to: number;
            total: number;
            links: Array<{
                url: string | null;
                label: string;
                active: boolean;
            }>;
        },
    );

    const categories = $derived(
        page.props.categories as Array<{
            id: number;
            name: string;
        }> | null,
    );

    const filters = $derived(
        page.props.filters as {
            type?: string;
            category_id?: string;
            payment_method?: string;
            search?: string;
            date_from?: string;
            date_to?: string;
        },
    );

    let search = $state(filters.search || '');
    let showFilters = $state(false);

    const paymentMethods = {
        cash: 'كاش',
        credit_card: 'بطاقة ائتمان',
        digital_wallet: 'محفظة رقمية',
        bank_transfer: 'تحويل بنكي',
    };

    function handleSearch() {
        const params = new URLSearchParams();

        if (search) {
            params.set('search', search);
        }

        if (filters.type) {
            params.set('type', filters.type);
        }

        if (filters.category_id) {
            params.set('category_id', filters.category_id);
        }

        if (filters.payment_method) {
            params.set('payment_method', filters.payment_method);
        }

        if (filters.date_from) {
            params.set('date_from', filters.date_from);
        }

        if (filters.date_to) {
            params.set('date_to', filters.date_to);
        }

        router.get(
            `/transactions?${params.toString()}`,
            {},
            { preserveState: true, preserveScroll: true },
        );
    }

    function applyFilter(key: string, value: string) {
        const params = new URLSearchParams(window.location.search);

        if (value && value !== 'all') {
            params.set(key, value);
        } else {
            params.delete(key);
        }

        params.set('page', '1');
        router.get(
            `/transactions?${params.toString()}`,
            {},
            { preserveState: true, preserveScroll: true },
        );
    }

    function deleteTransaction(id: number) {
        if (confirm('هل أنت متأكد من حذف هذه المعاملة؟')) {
            router.delete(`/transactions/${id}`, {
                preserveScroll: true,
            });
        }
    }
</script>

<AppHead title="المعاملات" />

<div class="flex flex-col gap-6 p-4">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold tracking-tight">المعاملات</h2>
            <p class="text-muted-foreground">جميع مصاريفك وإيراداتك</p>
        </div>
        <Button asChild>
            <Link href={toUrl('/transactions/create')}>
                <Plus class="size-4" />
                <span>إضافة معاملة</span>
            </Link>
        </Button>
    </div>

    <div class="flex items-center gap-2">
        <div class="relative flex-1">
            <Search
                class="absolute right-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground"
            />
            <Input
                class="pr-10"
                placeholder="بحث في الوصف..."
                bind:value={search}
                onkeydown={(e) => {
                    if (e.key === 'Enter') {
                        handleSearch();
                    }
                }}
            />
        </div>
        <Button
            variant="outline"
            size="icon"
            onclick={() => (showFilters = !showFilters)}
        >
            <Filter class="size-4" />
        </Button>
    </div>

    {#if showFilters}
        <Card>
            <CardContent class="grid gap-4 pt-6 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <label class="mb-1 block text-sm font-medium">النوع</label>
                    <Select
                        value={filters.type || 'all'}
                        onValueChange={(v) => applyFilter('type', v)}
                    >
                        <SelectTrigger>
                            <span
                                class={filters.type
                                    ? ''
                                    : 'text-muted-foreground'}
                            >
                                {filters.type === 'expense'
                                    ? 'مصروف'
                                    : filters.type === 'income'
                                      ? 'إيراد'
                                      : 'الكل'}
                            </span>
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">الكل</SelectItem>
                            <SelectItem value="expense">مصروف</SelectItem>
                            <SelectItem value="income">إيراد</SelectItem>
                        </SelectContent>
                    </Select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">التصنيف</label
                    >
                    <Select
                        value={filters.category_id || 'all'}
                        onValueChange={(v) => applyFilter('category_id', v)}
                    >
                        <SelectTrigger>
                            <span
                                class={filters.category_id
                                    ? ''
                                    : 'text-muted-foreground'}
                            >
                                {categories?.find(
                                    (c) => String(c.id) === filters.category_id,
                                )?.name || 'الكل'}
                            </span>
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">الكل</SelectItem>
                            {#each categories || [] as cat (cat.id)}
                                <SelectItem value={String(cat.id)}
                                    >{cat.name}</SelectItem
                                >
                            {/each}
                        </SelectContent>
                    </Select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium"
                        >طريقة الدفع</label
                    >
                    <Select
                        value={filters.payment_method || 'all'}
                        onValueChange={(v) => applyFilter('payment_method', v)}
                    >
                        <SelectTrigger>
                            <span
                                class={filters.payment_method
                                    ? ''
                                    : 'text-muted-foreground'}
                            >
                                {paymentMethods[
                                    filters.payment_method as keyof typeof paymentMethods
                                ] || 'الكل'}
                            </span>
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">الكل</SelectItem>
                            {#each Object.entries(paymentMethods) as [key, label] (key)}
                                <SelectItem value={key}>{label}</SelectItem>
                            {/each}
                        </SelectContent>
                    </Select>
                </div>
            </CardContent>
        </Card>
    {/if}

    {#if transactions?.data?.length}
        <div class="rounded-xl border">
            {#each transactions.data as tx (tx.id)}
                <div class="flex items-center gap-4 border-b p-4 last:border-0">
                    <div
                        class="flex size-10 items-center justify-center rounded-full {tx.type ===
                        'income'
                            ? 'bg-green-100 text-green-600'
                            : 'bg-red-100 text-red-600'}"
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
                            {tx.category?.name || 'بدون تصنيف'}
                            · {paymentMethods[
                                tx.payment_method as keyof typeof paymentMethods
                            ] || tx.payment_method}
                            · {tx.transaction_date}
                        </p>
                    </div>
                    <div
                        class="font-semibold {tx.type === 'income'
                            ? 'text-green-600'
                            : 'text-red-600'}"
                        dir="ltr"
                    >
                        {tx.type === 'income'
                            ? '+'
                            : '-'}{tx.amount?.toLocaleString('ar-SA')} ر.س
                    </div>
                    <div class="flex gap-1">
                        <Button variant="ghost" size="icon" asChild>
                            <Link href={toUrl(`/transactions/${tx.id}/edit`)}>
                                <Pencil class="size-4" />
                            </Link>
                        </Button>
                        <Button
                            variant="ghost"
                            size="icon"
                            onclick={() => deleteTransaction(tx.id)}
                        >
                            <Trash2 class="size-4 text-red-500" />
                        </Button>
                    </div>
                </div>
            {/each}
        </div>

        {#if transactions.last_page > 1}
            <div class="flex items-center justify-center gap-2">
                {#each transactions.links as link (link.label)}
                    {#if link.url}
                        <Button
                            variant={link.active ? 'default' : 'outline'}
                            size="sm"
                            disabled={!link.url}
                            onclick={() =>
                                router.get(
                                    link.url!,
                                    {},
                                    {
                                        preserveState: true,
                                        preserveScroll: true,
                                    },
                                )}
                        >
                            {link.label
                                .replace('&laquo;', '«')
                                .replace('&raquo;', '»')}
                        </Button>
                    {/if}
                {/each}
            </div>
        {/if}

        <p class="text-center text-sm text-muted-foreground">
            عرض {transactions.from} - {transactions.to} من إجمالي {transactions.total}
            معاملة
        </p>
    {:else}
        <div
            class="flex flex-col items-center justify-center rounded-xl border py-12 text-center"
        >
            <ArrowDownRight class="size-12 text-muted-foreground/50" />
            <p class="mt-4 text-muted-foreground">لا توجد معاملات</p>
            <Button variant="outline" class="mt-4" asChild>
                <Link href={toUrl('/transactions/create')}>أضف أول معاملة</Link>
            </Button>
        </div>
    {/if}
</div>
