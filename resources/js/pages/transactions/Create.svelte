<script module lang="ts">
    export const layout = {
        breadcrumbs: [
            { title: 'المعاملات', href: '/transactions' },
            { title: 'إضافة معاملة', href: '/transactions/create' },
        ],
    };
</script>

<script lang="ts">
    import { page, router } from '@inertiajs/svelte';
    import { Link } from '@inertiajs/svelte';
    import ArrowRight from 'lucide-svelte/icons/arrow-right';
    import AppHead from '@/components/AppHead.svelte';
    import Heading from '@/components/Heading.svelte';
    import InputError from '@/components/InputError.svelte';
    import { Button } from '@/components/ui/button';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import {
        Select,
        SelectContent,
        SelectItem,
        SelectTrigger,
    } from '@/components/ui/select';
    import { Textarea } from '@/components/ui/textarea';
    import { toUrl } from '@/lib/utils';

    const categories = $derived(
        page.props.categories as Array<{
            id: number;
            name: string;
            type: string;
        }> | null,
    );

    const errors = $derived(page.props.errors as Record<string, string>);

    let form = $state({
        type: 'expense',
        amount: '',
        description: '',
        category_id: '',
        payment_method: 'cash',
        transaction_date: new Date().toISOString().slice(0, 10),
        notes: '',
        receipt_image: null as File | null,
    });

    let processing = $state(false);

    const paymentMethods: Record<string, string> = {
        cash: 'كاش',
        credit_card: 'بطاقة ائتمان',
        digital_wallet: 'محفظة رقمية',
        bank_transfer: 'تحويل بنكي',
    };

    function submit(e: SubmitEvent) {
        e.preventDefault();
        processing = true;

        const data = new FormData();
        data.append('type', form.type);
        data.append('amount', form.amount);
        data.append('description', form.description);
        data.append('category_id', form.category_id);
        data.append('payment_method', form.payment_method);
        data.append('transaction_date', form.transaction_date);
        data.append('notes', form.notes);

        if (form.receipt_image) {
            data.append('receipt_image', form.receipt_image);
        }

        router.post('/transactions', data, {
            onFinish: () => {
                processing = false;
            },
        });
    }

    const filteredCategories = $derived(
        (categories || []).filter(
            (c) => c.type === form.type || c.type === 'both',
        ),
    );
</script>

<AppHead title="إضافة معاملة" />

<div class="flex flex-col gap-6 p-4">
    <Heading
        title="إضافة معاملة جديدة"
        description="أدخل تفاصيل المعاملة الجديدة"
    />

    <form onsubmit={submit} class="max-w-2xl space-y-6">
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <Label>نوع المعاملة</Label>
                <div class="mt-2 flex gap-2">
                    <Button
                        type="button"
                        variant={form.type === 'expense'
                            ? 'default'
                            : 'outline'}
                        class="flex-1"
                        onclick={() => {
                            form.type = 'expense';
                            form.category_id = '';
                        }}
                    >
                        مصروف
                    </Button>
                    <Button
                        type="button"
                        variant={form.type === 'income' ? 'default' : 'outline'}
                        class="flex-1"
                        onclick={() => {
                            form.type = 'income';
                            form.category_id = '';
                        }}
                    >
                        إيراد
                    </Button>
                </div>
                <InputError message={errors.type} />
            </div>

            <div>
                <Label for="amount">المبلغ (ر.س)</Label>
                <Input
                    id="amount"
                    type="number"
                    step="0.01"
                    min="0.01"
                    class="mt-2"
                    placeholder="0.00"
                    bind:value={form.amount}
                    required
                />
                <InputError message={errors.amount} />
            </div>
        </div>

        <div>
            <Label for="description">الوصف</Label>
            <Input
                id="description"
                class="mt-2"
                placeholder="مثال: غداء في مطعم"
                bind:value={form.description}
            />
            <InputError message={errors.description} />
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <Label>التصنيف</Label>
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
                            {filteredCategories.find(
                                (c) => String(c.id) === form.category_id,
                            )?.name || 'اختر التصنيف'}
                        </span>
                    </SelectTrigger>
                    <SelectContent>
                        {#each filteredCategories as cat (cat.id)}
                            <SelectItem value={String(cat.id)}
                                >{cat.name}</SelectItem
                            >
                        {/each}
                    </SelectContent>
                </Select>
                <InputError message={errors.category_id} />
            </div>

            <div>
                <Label>طريقة الدفع</Label>
                <Select
                    value={form.payment_method}
                    onValueChange={(v) => (form.payment_method = v)}
                >
                    <SelectTrigger class="mt-2">
                        <span
                            >{paymentMethods[
                                form.payment_method as keyof typeof paymentMethods
                            ]}</span
                        >
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="cash">كاش</SelectItem>
                        <SelectItem value="credit_card">بطاقة ائتمان</SelectItem
                        >
                        <SelectItem value="digital_wallet"
                            >محفظة رقمية</SelectItem
                        >
                        <SelectItem value="bank_transfer">تحويل بنكي</SelectItem
                        >
                    </SelectContent>
                </Select>
                <InputError message={errors.payment_method} />
            </div>
        </div>

        <div>
            <Label for="transaction_date">التاريخ</Label>
            <Input
                id="transaction_date"
                type="date"
                class="mt-2"
                bind:value={form.transaction_date}
                required
            />
            <InputError message={errors.transaction_date} />
        </div>

        <div>
            <Label for="notes">ملاحظات</Label>
            <Textarea
                id="notes"
                class="mt-2"
                rows={3}
                placeholder="أي ملاحظات إضافية..."
                bind:value={form.notes}
            />
            <InputError message={errors.notes} />
        </div>

        <div>
            <Label for="receipt_image">صورة الفاتورة (اختياري)</Label>
            <Input
                id="receipt_image"
                type="file"
                accept="image/*"
                class="mt-2"
                onchange={(e) =>
                    (form.receipt_image =
                        (e.target as HTMLInputElement).files?.[0] || null)}
            />
            <InputError message={errors.receipt_image} />
        </div>

        <div class="flex items-center gap-4">
            <Button type="submit" disabled={processing}>حفظ المعاملة</Button>
            <Button variant="outline" asChild>
                <Link href={toUrl('/transactions')}>
                    <ArrowRight class="size-4" />
                    <span>رجوع</span>
                </Link>
            </Button>
        </div>
    </form>
</div>
