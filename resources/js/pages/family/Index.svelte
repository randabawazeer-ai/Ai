<script module lang="ts">
    export const layout = {
        breadcrumbs: [{ title: 'العائلة', href: '/family' }],
    };
</script>

<script lang="ts">
    import { page, router } from '@inertiajs/svelte';
    import LogOut from 'lucide-svelte/icons/log-out';
    import Mail from 'lucide-svelte/icons/mail';
    import Plus from 'lucide-svelte/icons/plus';
    import Shield from 'lucide-svelte/icons/shield';
    import Users from 'lucide-svelte/icons/users';
    import AppHead from '@/components/AppHead.svelte';
    import { Avatar, AvatarFallback } from '@/components/ui/avatar';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import {
        Card,
        CardContent,
        CardHeader,
        CardTitle,
    } from '@/components/ui/card';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';

    const family = $derived(
        page.props.family as {
            id: number;
            name: string;
            created_by: number;
        } | null,
    );
    const members = $derived(
        page.props.members as Array<{
            id: number;
            name: string;
            email: string;
            pivot: { role: string };
        }> | null,
    );
    const invitations = $derived(
        page.props.invitations as Array<{
            id: number;
            email: string;
            status: string;
            inviter: { name: string };
        }> | null,
    );
    const pendingInvitation = $derived(
        page.props.pendingInvitation as {
            id: number;
            family: { name: string };
        } | null,
    );
    const errors = $derived(page.props.errors as Record<string, string>);

    let showCreate = $state(!family);
    let inviteEmail = $state('');
    let familyName = $state('');
    let processing = $state(false);

    function createFamily() {
        processing = true;
        router.post(
            '/family',
            { name: familyName },
            {
                onFinish: () => {
                    processing = false;
                    showCreate = false;
                },
            },
        );
    }

    function sendInvite() {
        processing = true;
        router.post(
            '/family/invite',
            { email: inviteEmail },
            {
                onFinish: () => {
                    processing = false;
                    inviteEmail = '';
                },
            },
        );
    }

    function leaveFamily() {
        if (confirm('هل أنت متأكد من مغادرة العائلة؟')) {
            router.delete('/family/leave', { preserveScroll: true });
        }
    }

    function acceptInvitation(invId: number) {
        router.post('/family/join', {}, { data: { invitation_id: invId } });
    }
</script>

<AppHead title="العائلة" />

<div class="flex flex-col gap-6 p-4">
    <h2 class="text-2xl font-bold tracking-tight">العائلة</h2>

    {#if pendingInvitation}
        <Card
            class="border-primary/20 bg-primary/5 dark:border-primary/30 dark:bg-primary/10"
        >
            <CardContent class="flex items-center gap-4 p-4">
                <Mail class="size-8 text-primary" />
                <div class="flex-1 text-right">
                    <p class="font-semibold">
                        دعوة للانضمام إلى {pendingInvitation.family.name}
                    </p>
                    <p class="text-sm text-muted-foreground">
                        تمت دعوتك للانضمام إلى هذه العائلة
                    </p>
                </div>
                <Button
                    size="sm"
                    onclick={() => acceptInvitation(pendingInvitation.id)}
                    >قبول</Button
                >
            </CardContent>
        </Card>
    {/if}

    {#if showCreate}
        <Card>
            <CardHeader>
                <CardTitle>إنشاء عائلة جديدة</CardTitle>
            </CardHeader>
            <CardContent>
                <form
                    onsubmit={(e) => {
                        e.preventDefault();
                        createFamily();
                    }}
                    class="space-y-4"
                >
                    <div>
                        <Label for="family-name">اسم العائلة</Label>
                        <Input
                            id="family-name"
                            class="mt-2"
                            placeholder="مثال: عائلة الأحمد"
                            bind:value={familyName}
                            required
                        />
                        {#if errors.name}<p
                                class="mt-1 text-sm text-destructive"
                            >
                                {errors.name}
                            </p>{/if}
                    </div>
                    <Button type="submit" disabled={processing || !familyName}
                        >إنشاء العائلة</Button
                    >
                </form>
            </CardContent>
        </Card>
    {:else if family}
        <Card>
            <CardHeader>
                <div class="flex items-center justify-between">
                    <CardTitle>{family.name}</CardTitle>
                    <Button variant="ghost" size="sm" onclick={leaveFamily}>
                        <LogOut class="size-4" />
                        <span>مغادرة</span>
                    </Button>
                </div>
            </CardHeader>
            <CardContent class="space-y-4">
                <div>
                    <h4 class="mb-2 text-sm font-medium text-muted-foreground">
                        الأعضاء ({members?.length || 0})
                    </h4>
                    <div class="space-y-2">
                        {#each members || [] as member (member.id)}
                            <div
                                class="flex items-center gap-3 rounded-lg border p-3"
                            >
                                <Avatar class="size-9">
                                    <AvatarFallback
                                        >{member.name[0]}</AvatarFallback
                                    >
                                </Avatar>
                                <div class="flex-1 text-right">
                                    <p class="text-sm font-medium">
                                        {member.name}
                                    </p>
                                    <p class="text-xs text-muted-foreground">
                                        {member.email}
                                    </p>
                                </div>
                                {#if member.pivot?.role === 'admin'}
                                    <Badge variant="secondary" class="gap-1">
                                        <Shield class="size-3" /> مشرف
                                    </Badge>
                                {/if}
                            </div>
                        {/each}
                    </div>
                </div>

                <div class="border-t pt-4">
                    <h4 class="mb-2 text-sm font-medium text-muted-foreground">
                        دعوة عضو جديد
                    </h4>
                    <form
                        onsubmit={(e) => {
                            e.preventDefault();
                            sendInvite();
                        }}
                        class="flex gap-2"
                    >
                        <Input
                            placeholder="البريد الإلكتروني"
                            bind:value={inviteEmail}
                            type="email"
                            required
                        />
                        <Button
                            type="submit"
                            disabled={processing || !inviteEmail}
                            size="sm"
                        >
                            <Plus class="size-4" />
                            <span>دعوة</span>
                        </Button>
                    </form>
                    {#if errors.email}<p class="mt-1 text-sm text-destructive">
                            {errors.email}
                        </p>{/if}
                </div>

                {#if invitations?.length}
                    <div class="border-t pt-4">
                        <h4
                            class="mb-2 text-sm font-medium text-muted-foreground"
                        >
                            الدعوات المعلقة
                        </h4>
                        {#each invitations as inv (inv.id)}
                            <div
                                class="flex items-center gap-2 rounded-lg border p-2 text-sm"
                            >
                                <Mail class="size-4 text-muted-foreground" />
                                <span class="flex-1">{inv.email}</span>
                                <Badge variant="outline">قيد الانتظار</Badge>
                            </div>
                        {/each}
                    </div>
                {/if}
            </CardContent>
        </Card>
    {/if}

    {#if !family && !showCreate && !pendingInvitation}
        <div
            class="flex flex-col items-center justify-center rounded-xl border py-12 text-center"
        >
            <Users class="size-12 text-muted-foreground/50" />
            <p class="mt-4 text-muted-foreground">
                أنشئ عائلة وشارك مصاريفك مع أفراد أسرتك
            </p>
            <Button class="mt-4" onclick={() => (showCreate = true)}
                >إنشاء عائلة</Button
            >
        </div>
    {/if}
</div>
