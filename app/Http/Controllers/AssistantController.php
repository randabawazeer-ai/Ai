<?php

namespace App\Http\Controllers;

use App\Ai\Agents\FinanceAssistant;
use Illuminate\Http\Request;
use Illuminate\Http\StreamedEvent;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Ai\Messages\Message;
use Laravel\Ai\Streaming\Events\Error as ErrorEvent;
use Laravel\Ai\Streaming\Events\TextDelta;
use Laravel\Ai\Streaming\Events\ToolCall;
use Laravel\Ai\Streaming\Events\ToolResult;

class AssistantController extends Controller
{
    /**
     * The maximum number of conversation messages sent to the model.
     */
    protected const MAX_HISTORY = 20;

    /**
     * The maximum length of a single message.
     */
    protected const MAX_MESSAGE_LENGTH = 4000;

    /**
     * Render the assistant chat page.
     */
    public function index(Request $request): Response
    {
        return Inertia::render('assistant/Index');
    }

    /**
     * Stream the agent's response for the given conversation.
     */
    public function stream(Request $request)
    {
        $messages = $request->input('messages');

        if (! is_array($messages) || count($messages) === 0) {
            return response()->json(['message' => 'المطلوب مصفوفة messages تحتوي على رسالة واحدة أو أكثر.'], 422);
        }

        if (count($messages) > 50) {
            return response()->json(['message' => 'عدد الرسائل يتجاوز الحد المسموح.'], 422);
        }

        $messages = $this->sanitizeMessages($messages);

        if ($messages === []) {
            return response()->json(['message' => 'لا توجد رسائل صالحة.'], 422);
        }

        // The last message is the new user prompt; everything before it is history.
        $prompt = array_pop($messages);

        if ($prompt->role->value !== 'user' || trim($prompt->content) === '') {
            return response()->json(['message' => 'آخر رسالة يجب أن تكون رسالة مستخدم.'], 422);
        }

        $agent = new FinanceAssistant($request->user(), $messages);

        // The agent may take longer than the default 30s execution limit
        // while streaming from the AI provider.
        set_time_limit(180);

        return response()->eventStream(function () use ($agent, $prompt) {
            $text = '';

            foreach ($agent->stream($prompt->content) as $event) {
                if (connection_aborted()) {
                    break;
                }

                if ($event instanceof TextDelta) {
                    $text .= $event->delta;

                    yield new StreamedEvent('text_delta', [
                        'delta' => $event->delta,
                    ]);

                    continue;
                }

                if ($event instanceof ToolCall) {
                    yield new StreamedEvent('tool_call', [
                        'tool' => $event->toolCall->name,
                        'arguments' => $event->toolCall->arguments,
                        'tool_id' => $event->toolCall->id,
                    ]);

                    continue;
                }

                if ($event instanceof ToolResult) {
                    yield new StreamedEvent('tool_result', [
                        'tool' => $event->toolResult->name,
                        'ok' => $event->successful,
                        'result' => $event->toolResult->result,
                        'error' => $event->error,
                        'tool_id' => $event->toolResult->id,
                    ]);

                    continue;
                }

                if ($event instanceof ErrorEvent) {
                    yield new StreamedEvent('error', [
                        'message' => $event->message,
                        'recoverable' => $event->recoverable,
                    ]);
                }
            }

            yield new StreamedEvent('done', [
                'text' => trim($text),
            ]);
        }, endStreamWith: null);
    }

    /**
     * Sanitize and normalize the incoming conversation messages.
     *
     * @return Message[]
     */
    protected function sanitizeMessages(array $messages): array
    {
        $clean = [];

        foreach (array_slice($messages, -self::MAX_HISTORY) as $message) {
            $role = $message['role'] ?? null;
            $content = is_string($message['content'] ?? null) ? trim($message['content']) : '';

            if (! in_array($role, ['user', 'assistant'], true) || $content === '') {
                continue;
            }

            $clean[] = new Message(
                role: $role,
                content: mb_substr($content, 0, self::MAX_MESSAGE_LENGTH),
            );
        }

        return $clean;
    }
}
