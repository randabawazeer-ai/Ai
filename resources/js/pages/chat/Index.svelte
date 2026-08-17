<script module lang="ts">
    export const layout = {
        breadcrumbs: [{ title: 'المساعد الذكي', href: '/chat' }],
    };
</script>

<script lang="ts">
    import { page } from '@inertiajs/svelte';
    import Bot from 'lucide-svelte/icons/bot';
    import Send from 'lucide-svelte/icons/send';
    import Sparkles from 'lucide-svelte/icons/sparkles';
    import User from 'lucide-svelte/icons/user';
    import AppHead from '@/components/AppHead.svelte';
    import { Button } from '@/components/ui/button';
    import { Card, CardContent } from '@/components/ui/card';
    import { Input } from '@/components/ui/input';

    const initialMessages = $derived(
        page.props.messages as Array<{ role: string; content: string }>,
    );

    let messages = $state(initialMessages || []);
    let input = $state('');
    let loading = $state(false);
    let chatContainer: HTMLDivElement;

    function scrollToBottom() {
        if (chatContainer) {
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }
    }

    $effect(() => {
        scrollToBottom();
    });

    async function sendMessage() {
        const text = input.trim();

        if (!text || loading) {
            return;
        }

        messages = [...messages, { role: 'user', content: text }];
        input = '';
        loading = true;

        try {
            const res = await fetch('/chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN':
                        (
                            document.querySelector(
                                'meta[name="csrf-token"]',
                            ) as HTMLMetaElement
                        )?.content || '',
                },
                body: JSON.stringify({ message: text }),
            });
            const data = await res.json();
            messages = data.messages;
        } catch {
            messages = [
                ...messages,
                { role: 'ai', content: 'عذراً، حدث خطأ. حاول مرة أخرى.' },
            ];
        } finally {
            loading = false;
        }
    }

    function handleKeydown(e: KeyboardEvent) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    }

    const suggestions = [
        'كم صرفت هذا الشهر؟',
        'كم باقي من الميزانية؟',
        'آخر المعاملات',
        'وين أقدر أوفر؟',
    ];
</script>

<AppHead title="المساعد الذكي" />

<div class="flex h-[calc(100vh-5rem)] flex-col">
    <div class="flex items-center gap-2 border-b px-4 py-3">
        <div
            class="flex size-9 items-center justify-center rounded-lg bg-primary text-primary-foreground"
        >
            <Sparkles class="size-5" />
        </div>
        <div>
            <h2 class="font-semibold">مساعد مدبّر</h2>
            <p class="text-xs text-muted-foreground">
                ذكاء اصطناعي · دائماً متصل
            </p>
        </div>
    </div>

    <div bind:this={chatContainer} class="flex-1 space-y-4 overflow-y-auto p-4">
        {#each messages as msg, i (i)}
            {#if msg.role === 'ai'}
                <div class="flex gap-3">
                    <div
                        class="flex size-8 shrink-0 items-center justify-center rounded-full bg-primary/10"
                    >
                        <Bot class="size-4 text-primary" />
                    </div>
                    <Card class="max-w-[85%] rounded-2xl">
                        <CardContent class="whitespace-pre-wrap p-3 text-sm">
                            {msg.content}
                        </CardContent>
                    </Card>
                </div>
            {:else}
                <div class="flex flex-row-reverse gap-3">
                    <div
                        class="flex size-8 shrink-0 items-center justify-center rounded-full bg-muted"
                    >
                        <User class="size-4" />
                    </div>
                    <Card
                        class="max-w-[85%] rounded-2xl bg-primary text-primary-foreground"
                    >
                        <CardContent class="whitespace-pre-wrap p-3 text-sm">
                            {msg.content}
                        </CardContent>
                    </Card>
                </div>
            {/if}
        {/each}

        {#if loading}
            <div class="flex gap-3">
                <div
                    class="flex size-8 shrink-0 items-center justify-center rounded-full bg-primary/10"
                >
                    <Bot class="size-4 text-primary" />
                </div>
                <Card>
                    <CardContent class="p-3">
                        <span class="flex gap-1">
                            <span
                                class="size-2 animate-bounce rounded-full bg-muted-foreground"
                                style="animation-delay:0ms"
                            ></span>
                            <span
                                class="size-2 animate-bounce rounded-full bg-muted-foreground"
                                style="animation-delay:150ms"
                            ></span>
                            <span
                                class="size-2 animate-bounce rounded-full bg-muted-foreground"
                                style="animation-delay:300ms"
                            ></span>
                        </span>
                    </CardContent>
                </Card>
            </div>
        {/if}

        {#if messages?.length === 1}
            <div class="flex flex-wrap justify-center gap-2 pt-8">
                {#each suggestions as s (s)}
                    <Button
                        variant="outline"
                        size="sm"
                        class="rounded-full"
                        onclick={() => {
                            input = s;
                            sendMessage();
                        }}
                    >
                        {s}
                    </Button>
                {/each}
            </div>
        {/if}
    </div>

    <div class="border-t p-3">
        <div class="flex gap-2">
            <Input
                class="flex-1"
                placeholder="اسألني عن مصاريفك..."
                bind:value={input}
                onkeydown={handleKeydown}
                disabled={loading}
            />
            <Button
                onclick={sendMessage}
                disabled={loading || !input.trim()}
                size="icon"
                class="hover:bg-primary/90"
            >
                <Send class="size-4" />
            </Button>
        </div>
    </div>
</div>
