<script module lang="ts">
    export const layout = {
        breadcrumbs: [{ title: 'التصنيفات', href: '/categories' }],
    };
</script>

<script lang="ts">
    import { page, router } from '@inertiajs/svelte';
    import Pencil from 'lucide-svelte/icons/pencil';
    import Plus from 'lucide-svelte/icons/plus';
    import Tag from 'lucide-svelte/icons/tag';
    import Trash2 from 'lucide-svelte/icons/trash-2';
    import AppHead from '@/components/AppHead.svelte';
    import InputError from '@/components/InputError.svelte';
    import { Button } from '@/components/ui/button';
    import { Card, CardContent } from '@/components/ui/card';
    import {
        Dialog,
        DialogContent,
        DialogHeader,
        DialogTitle,
        DialogTrigger,
    } from '@/components/ui/dialog';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import {
        Select,
        SelectContent,
        SelectItem,
        SelectTrigger,
    } from '@/components/ui/select';

    const defaultCategories = $derived(
        page.props.defaultCategories as Array<{
            id: number;
            name: string;
            icon: string;
            type: string;
        }> | null,
    );

    const customCategories = $derived(
        page.props.customCategories as Array<{
            id: number;
            name: string;
            icon: string;
            type: string;
        }> | null,
    );

    const errors = $derived(page.props.errors as Record<string, string>);

    let showAddDialog = $state(false);
    let editingCategory = $state<{
        id: number;
        name: string;
        type: string;
    } | null>(null);

    let newCategory = $state({ name: '', type: 'expense' });
    let editForm = $state({ name: '', type: 'expense' });

    let processing = $state(false);

    function addCategory(e: SubmitEvent) {
        e.preventDefault();
        processing = true;
        router.post(
            '/categories',
            {
                name: newCategory.name,
                type: newCategory.type,
            },
            {
                onSuccess: () => {
                    processing = false;
                    showAddDialog = false;
                    newCategory = { name: '', type: 'expense' };
                },
                onError: () => {
                    processing = false;
                },
            },
        );
    }

    function startEdit(cat: { id: number; name: string; type: string }) {
        editingCategory = cat;
        editForm = { name: cat.name, type: cat.type };
    }

    function saveEdit(e: SubmitEvent) {
        e.preventDefault();

        if (!editingCategory) {
            return;
        }

        processing = true;
        router.post(
            `/categories/${editingCategory.id}`,
            {
                _method: 'PATCH',
                name: editForm.name,
                type: editForm.type,
            },
            {
                onSuccess: () => {
                    processing = false;
                    editingCategory = null;
                },
                onError: () => {
                    processing = false;
                },
            },
        );
    }

    function deleteCategory(id: number) {
        if (confirm('هل أنت متأكد من حذف هذا التصنيف؟')) {
            router.delete(`/categories/${id}`, { preserveScroll: true });
        }
    }

    const typeLabel: Record<string, string> = {
        expense: 'مصروفات',
        income: 'إيرادات',
        both: 'مصروفات وإيرادات',
    };

    function typeColor(type: string): string {
        if (type === 'expense') {
            return 'text-expense';
        }

        if (type === 'income') {
            return 'text-income';
        }

        return 'text-primary';
    }
</script>

<AppHead title="التصنيفات" />

<div class="flex flex-col gap-6 p-4">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold tracking-tight">التصنيفات</h2>
            <p class="text-muted-foreground">
                إدارة تصنيفات المصاريف والإيرادات
            </p>
        </div>
        <Dialog open={showAddDialog} onOpenChange={(v) => (showAddDialog = v)}>
            <DialogTrigger asChild>
                <Button onclick={() => (showAddDialog = true)}>
                    <Plus class="size-4" />
                    <span>إضافة تصنيف</span>
                </Button>
            </DialogTrigger>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>إضافة تصنيف جديد</DialogTitle>
                </DialogHeader>
                <form onsubmit={addCategory} class="space-y-4">
                    <div>
                        <Label for="cat-name">اسم التصنيف</Label>
                        <Input
                            id="cat-name"
                            class="mt-2"
                            placeholder="مثال: ملابس"
                            bind:value={newCategory.name}
                            required
                        />
                        <InputError message={errors.name} />
                    </div>
                    <div>
                        <Label>نوع التصنيف</Label>
                        <Select
                            value={newCategory.type}
                            onValueChange={(v) => (newCategory.type = v)}
                        >
                            <SelectTrigger class="mt-2">
                                <span
                                    >{typeLabel[newCategory.type] ||
                                        'اختر النوع'}</span
                                >
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="expense">مصروفات</SelectItem>
                                <SelectItem value="income">إيرادات</SelectItem>
                                <SelectItem value="both"
                                    >مصروفات وإيرادات</SelectItem
                                >
                            </SelectContent>
                        </Select>
                        <InputError message={errors.type} />
                    </div>
                    <Button type="submit" disabled={processing}>حفظ</Button>
                </form>
            </DialogContent>
        </Dialog>
    </div>

    <div>
        <h3 class="mb-4 text-lg font-semibold">التصنيفات الجاهزة</h3>
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            {#each defaultCategories || [] as cat (cat.id)}
                <Card>
                    <CardContent class="flex items-center gap-3 p-4">
                        <Tag class="size-5 {typeColor(cat.type)}" />
                        <div class="flex-1 text-right">
                            <p class="font-medium">{cat.name}</p>
                            <span
                                class="mt-1 inline-block rounded-full bg-muted px-2 py-0.5 text-xs text-muted-foreground"
                                >{typeLabel[cat.type]}</span
                            >
                        </div>
                        <div class="text-xs text-muted-foreground">افتراضي</div>
                    </CardContent>
                </Card>
            {/each}
        </div>
    </div>

    <div>
        <h3 class="mb-4 text-lg font-semibold">تصنيفاتي المخصصة</h3>
        {#if customCategories?.length}
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                {#each customCategories as cat (cat.id)}
                    <Card>
                        <CardContent class="flex items-center gap-3 p-4">
                            <Tag class="size-5 {typeColor(cat.type)}" />
                            <div class="flex-1 text-right">
                                <p class="font-medium">{cat.name}</p>
                                <span
                                    class="mt-1 inline-block rounded-full bg-muted px-2 py-0.5 text-xs text-muted-foreground"
                                    >{typeLabel[cat.type]}</span
                                >
                            </div>
                            <Button
                                variant="ghost"
                                size="icon"
                                onclick={() => startEdit(cat)}
                            >
                                <Pencil class="size-4" />
                            </Button>
                            <Button
                                variant="ghost"
                                size="icon"
                                onclick={() => deleteCategory(cat.id)}
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
                <Tag class="size-12 text-muted-foreground/50" />
                <p class="mt-4 text-muted-foreground">لا توجد تصنيفات مخصصة</p>
                <p class="text-sm text-muted-foreground">
                    أضف تصنيفاتك الخاصة من الزر أعلاه
                </p>
            </div>
        {/if}
    </div>
</div>

{#if editingCategory}
    <Dialog
        open={!!editingCategory}
        onOpenChange={(v) => {
            if (!v) {
                editingCategory = null;
            }
        }}
    >
        <DialogContent>
            <DialogHeader>
                <DialogTitle>تعديل التصنيف</DialogTitle>
            </DialogHeader>
            <form onsubmit={saveEdit} class="space-y-4">
                <div>
                    <Label for="edit-name">اسم التصنيف</Label>
                    <Input
                        id="edit-name"
                        class="mt-2"
                        bind:value={editForm.name}
                        required
                    />
                    <InputError message={errors.name} />
                </div>
                <div>
                    <Label>نوع التصنيف</Label>
                    <Select
                        value={editForm.type}
                        onValueChange={(v) => (editForm.type = v)}
                    >
                        <SelectTrigger class="mt-2">
                            <span
                                >{typeLabel[editForm.type] ||
                                    'اختر النوع'}</span
                            >
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="expense">مصروفات</SelectItem>
                            <SelectItem value="income">إيرادات</SelectItem>
                            <SelectItem value="both"
                                >مصروفات وإيرادات</SelectItem
                            >
                        </SelectContent>
                    </Select>
                    <InputError message={errors.type} />
                </div>
                <Button type="submit" disabled={processing}
                    >حفظ التعديلات</Button
                >
            </form>
        </DialogContent>
    </Dialog>
{/if}
