<script module lang="ts">
    export const layout = {
        breadcrumbs: [{ title: 'المساعد الذكي', href: '/assistant' }],
    };
</script>

<script lang="ts">
    import Bot from 'lucide-svelte/icons/bot';
    import CirclePlus from 'lucide-svelte/icons/circle-plus';
    import LoaderCircle from 'lucide-svelte/icons/loader-circle';
    import PencilLine from 'lucide-svelte/icons/pencil-line';
    import Search from 'lucide-svelte/icons/search';
    import Send from 'lucide-svelte/icons/send';
    import Sparkles from 'lucide-svelte/icons/sparkles';
    import Square from 'lucide-svelte/icons/square';
    import Trash2 from 'lucide-svelte/icons/trash-2';
    import User from 'lucide-svelte/icons/user';
    import AppHead from '@/components/AppHead.svelte';
    import { Button } from '@/components/ui/button';
    import { Card, CardContent } from '@/components/ui/card';
    import { Input } from '@/components/ui/input';
    import { stream } from '@/routes/assistant';

    type ToolEvent = {
        tool: string;
        ok: boolean | null;
        summary?: string;
    };

    type ChatMessage = {
        role: 'user' | 'assistant';
        content: string;
        tools?: ToolEvent[];
    };

    const toolLabels: Record<string, string> = {
        ListTransactions: 'الاستعلام عن المعاملات',
        CreateTransactions: 'إضافة معاملات',
        UpdateTransactions: 'تعديل معاملات',
        DeleteTransactions: 'حذف معاملات',
    };

    function toolIcon(tool: string, ok: boolean | null) {
        switch (tool) {
            case 'CreateTransactions':
                return CirclePlus;
            case 'UpdateTransactions':
                return PencilLine;
            case 'DeleteTransactions':
                return Trash2;
            default:
                return Search;
        }
    }

    let messages = $state<ChatMessage[]>([]);
    let input = $state('');
    let loading = $state(false);
    let errorMessage = $state('');
    let abortController: AbortController | null = null;
    let chatContainer: HTMLDivElement;

    function scrollToBottom() {
        if (chatContainer) {
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }
    }

    $effect(() => {
        if (messages.length) {
            scrollToBottom();
        }
    });

    function parseToolResult(e: ToolEvent, raw: string): ToolEvent {
        try {
            const parsed = JSON.parse(raw);

            if (parsed && typeof parsed === 'object' && 'summary' in parsed) {
                return {
                    ...e,
                    ok: Boolean(parsed.ok),
                    summary: String(parsed.summary),
                };
            }
        } catch {
            // not JSON, show raw text as summary
        }

        return { ...e, ok: true, summary: raw };
    }

    async function sendMessage(text: string) {
        if (loading) {
            return;
        }

        const trimmed = text.trim();

        if (!trimmed) {
            return;
        }

        const userMessage: ChatMessage = { role: 'user', content: trimmed };
        const assistantMessage: ChatMessage = {
            role: 'assistant',
            content: '',
            tools: [],
        };

        messages = [...messages, userMessage, assistantMessage];
        input = '';
        loading = true;
        errorMessage = '';

        const csrf =
            (
                document.querySelector(
                    'meta[name="csrf-token"]',
                ) as HTMLMetaElement | null
            )?.content ?? '';

        abortController = new AbortController();

        try {
            const res = await fetch(stream().url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'text/event-stream',
                    'X-CSRF-TOKEN': csrf,
                },
                signal: abortController.signal,
                body: JSON.stringify({
                    messages: messages.slice(0, -1).map((m) => ({
                        role: m.role,
                        content: m.content,
                    })),
                }),
            });

            if (!res.ok) {
                const data = await res.json().catch(() => null);

                throw new Error(data?.message || `فشل الطلب (${res.status})`);
            }

            if (!res.body) {
                throw new Error('لا يدعم المتصفح البث المباشر.');
            }

            const reader = res.body.getReader();
            const decoder = new TextDecoder();
            let buffer = '';

            while (true) {
                const { done, value } = await reader.read();

                if (done) {
                    break;
                }

                buffer += decoder.decode(value, { stream: true });

                const events = buffer.split('\n\n');
                buffer = events.pop() ?? '';

                for (const block of events) {
                    let eventName = 'message';
                    const dataLines: string[] = [];

                    for (const line of block.split('\n')) {
                        if (line.startsWith('event:')) {
                            eventName = line.slice(6).trim();
                        } else if (line.startsWith('data:')) {
                            dataLines.push(line.slice(5).trim());
                        }
                    }

                    const raw = dataLines.join('\n');

                    if (!raw) {
                        continue;
                    }

                    let payload: Record<string, unknown>;

                    try {
                        payload = JSON.parse(raw);
                    } catch {
                        continue;
                    }

                    if (eventName === 'text_delta') {
                        assistantMessage.content += String(payload.delta ?? '');
                    } else if (eventName === 'tool_call') {
                        const tool = String(payload.tool ?? '');

                        if (assistantMessage.tools) {
                            assistantMessage.tools.push({ tool, ok: null });
                        }
                    } else if (eventName === 'tool_result') {
                        const active = assistantMessage.tools?.find(
                            (t) => t.ok === null,
                        );

                        if (active) {
                            Object.assign(
                                active,
                                parseToolResult(
                                    active,
                                    String(payload.result ?? ''),
                                ),
                            );
                        }
                    } else if (eventName === 'error') {
                        assistantMessage.content += `\n\n⚠️ ${String(payload.message ?? 'حدث خطأ.').replace(/[<>&]/g, '')}`;
                    } else if (eventName === 'done') {
                        break;
                    }
                }
            }
        } catch (err) {
            if (err instanceof DOMException && err.name === 'AbortError') {
                assistantMessage.content += '\n\n(متوقف يدوياً)';
            } else {
                assistantMessage.content += `\n\nعذراً، حدث خطأ: ${err instanceof Error ? err.message : 'حاول مجدداً'}`;
            }
        } finally {
            loading = false;
            abortController = null;
        }
    }

    function handleSend() {
        sendMessage(input);
    }

    function handleKeydown(e: KeyboardEvent) {
        if (e.key === 'Enter' && !e.shiftKey && !e.isComposing) {
            e.preventDefault();
            sendMessage(input);
        }
    }

    function stopStreaming() {
        abortController?.abort();
    }

    const suggestions = [
        'كم صرفت هذا الشهر؟',
        'ما هي أكبر فئة إنفاق؟',
        'أضف مصروف 200 ريال على طعام',
        'أين يمكنني الادخار؟',
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
        <div class="flex-1">
            <h2 class="font-semibold">مساعد مدبّر الذكي</h2>
            <p class="text-xs text-muted-foreground">
                وكيل ذكي يمكنه الاستعلام والإضافة والتعديل والحذف
            </p>
        </div>
    </div>

    <div bind:this={chatContainer} class="flex-1 space-y-4 overflow-y-auto p-4">
        {#if messages.length === 0}
            <div
                class="flex h-full flex-col items-center justify-center gap-6 pb-16"
            >
                <div
                    class="flex size-16 items-center justify-center rounded-2xl bg-primary/10"
                >
                    <Bot class="size-8 text-primary" />
                </div>
                <div class="max-w-md text-center">
                    <h3 class="text-xl font-bold">كيف أساعدك اليوم؟</h3>
                    <p class="mt-2 text-sm text-muted-foreground">
                        اسألني عن مصاريفك، أضف أو عدّل أو احذف معاملات، واحصل
                        على تحليل ذكي لأموالك.
                    </p>
                </div>
                <div class="flex flex-wrap justify-center gap-2">
                    {#each suggestions as s (s)}
                        <Button
                            variant="outline"
                            size="sm"
                            class="rounded-full"
                            onclick={() => sendMessage(s)}
                        >
                            {s}
                        </Button>
                    {/each}
                </div>
            </div>
        {:else}
            {#each messages as msg, i (i)}
                {#if msg.role === 'user'}
                    <div class="flex flex-row-reverse gap-3">
                        <div
                            class="flex size-8 shrink-0 items-center justify-center rounded-full bg-muted"
                        >
                            <User class="size-4" />
                        </div>
                        <Card
                            class="max-w-[85%] rounded-2xl bg-primary text-primary-foreground"
                        >
                            <CardContent
                                class="whitespace-pre-wrap p-3 text-sm"
                            >
                                {msg.content}
                            </CardContent>
                        </Card>
                    </div>
                {:else}
                    <div class="flex gap-3">
                        <div
                            class="flex size-8 shrink-0 items-center justify-center rounded-full bg-primary/10"
                        >
                            <Bot class="size-4 text-primary" />
                        </div>
                        <div class="flex max-w-[85%] flex-col gap-2">
                            {#if msg.tools?.length}
                                <div class="flex flex-wrap gap-2">
                                    {#each msg.tools as tool (tool.tool + '_' + (tool.ok ?? 'pending'))}
                                        <div
                                            class="flex items-center gap-1.5 rounded-full border bg-muted/50 px-2.5 py-1 text-xs"
                                        >
                                            {#snippet icon()}
                                                {@const Icon = toolIcon(
                                                    tool.tool,
                                                    tool.ok,
                                                )}
                                                <Icon
                                                    class="size-3.5 shrink-0 text-primary"
                                                />
                                            {/snippet}
                                            {@render icon()}
                                            <span
                                                >{toolLabels[tool.tool] ??
                                                    tool.tool}</span
                                            >
                                            {#if tool.ok === null}
                                                <LoaderCircle
                                                    class="size-3 animate-spin text-muted-foreground"
                                                />
                                            {:else if tool.ok}
                                                <span class="text-green-600"
                                                    >✓</span
                                                >
                                            {:else}
                                                <span class="text-destructive"
                                                    >✕</span
                                                >
                                            {/if}
                                        </div>
                                    {/each}
                                </div>
                                {#if (msg.tools ?? []).filter((t) => t.ok === true && t.summary).length}
                                    <div
                                        class="rounded-lg border bg-muted/30 p-2 text-xs text-muted-foreground"
                                    >
                                        {#each (msg.tools ?? []).filter((t) => t.ok === true && t.summary?.trim()) as tool (tool.tool)}
                                            <p class="whitespace-pre-wrap">
                                                {tool.summary}
                                            </p>
                                        {/each}
                                    </div>
                                {/if}
                            {/if}
                            {#if msg.content || loading}
                                <Card class="rounded-2xl">
                                    {#if msg.content}
                                        <CardContent
                                            class="whitespace-pre-wrap p-3 text-sm"
                                        >
                                            {msg.content}
                                        </CardContent>
                                    {:else}
                                        <CardContent
                                            class="flex items-center gap-2 p-3 text-sm"
                                        >
                                            <LoaderCircle
                                                class="size-4 animate-spin"
                                            />
                                            <span>جارٍ التفكير...</span>
                                        </CardContent>
                                    {/if}
                                </Card>
                            {/if}
                        </div>
                    </div>
                {/if}
            {/each}
        {/if}
    </div>

    <div class="border-t p-3">
        {#if errorMessage}
            <p class="mb-2 text-xs text-destructive">{errorMessage}</p>
        {/if}
        <form
            class="flex gap-2"
            onsubmit={(e) => {
                e.preventDefault();
                sendMessage(input);
            }}
        >
            <Input
                class="flex-1"
                placeholder="اسألني عن مصاريفك، أو اطلب إضافة معاملة..."
                bind:value={input}
                onkeydown={handleKeydown}
                disabled={loading}
            />
            {#if loading}
                <Button
                    type="button"
                    onclick={stopStreaming}
                    variant="secondary"
                    size="icon"
                    aria-label="إيقاف"
                >
                    <Square class="size-4" />
                </Button>
            {:else}
                <Button
                    type="submit"
                    disabled={!input.trim()}
                    size="icon"
                    class="hover:bg-primary/90"
                    aria-label="إرسال"
                >
                    <Send class="size-4" />
                </Button>
            {/if}
        </form>
    </div>
</div>
