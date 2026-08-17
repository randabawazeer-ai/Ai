<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ChatController extends Controller
{
    public function index(Request $request): Response
    {
        $messages = $request->session()->get('chat_messages', [
            ['role' => 'ai', 'content' => 'مرحباً! أنا مساعد مدبّر. اسألني عن مصاريفك، كم صرفت، وين تقدر توفر، أو حتى اطلب مني أضيف معاملة.'],
        ]);

        return Inertia::render('chat/Index', [
            'messages' => $messages,
        ]);
    }

    public function send(Request $request): JsonResponse
    {
        $userMessage = $request->input('message');
        $messages = $request->session()->get('chat_messages', []);

        $messages[] = ['role' => 'user', 'content' => $userMessage];

        $aiResponse = $this->generateResponse($userMessage, $request);

        $messages[] = ['role' => 'ai', 'content' => $aiResponse];

        $request->session()->put('chat_messages', array_slice($messages, -50));

        return response()->json([
            'response' => $aiResponse,
            'messages' => $messages,
        ]);
    }

    private function generateResponse(string $message, Request $request): string
    {
        $userId = $request->user()->id;
        $currentMonth = now()->format('Y-m');

        $message = trim(mb_strtolower($message));

        // Query transactions
        $totalExpenses = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereRaw("strftime('%Y-%m', transaction_date) = ?", [$currentMonth])
            ->sum('amount');

        $totalIncome = Transaction::where('user_id', $userId)
            ->where('type', 'income')
            ->whereRaw("strftime('%Y-%m', transaction_date) = ?", [$currentMonth])
            ->sum('amount');

        $topCategory = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereRaw("strftime('%Y-%m', transaction_date) = ?", [$currentMonth])
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->selectRaw('categories.name, SUM(transactions.amount) as total')
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total')
            ->first();

        $budget = Budget::where('user_id', $userId)
            ->where('month', $currentMonth)
            ->whereNull('category_id')
            ->first();

        $recentTransactions = Transaction::where('user_id', $userId)
            ->with('category')
            ->latest('transaction_date')
            ->limit(5)
            ->get();

        // Match intents
        if (str_contains($message, 'كم صرفت') || str_contains($message, 'مصاريف') || str_contains($message, 'صرفت')) {
            $budgetInfo = '';
            if ($budget && $budget->amount > 0) {
                $remaining = $budget->amount - $totalExpenses;
                $pct = round(($totalExpenses / $budget->amount) * 100);
                $budgetInfo = "ميزانيتك الشهرية {$budget->amount} ر.س، صرفت {$totalExpenses} ر.س ({$pct}%)";
                if ($remaining > 0) {
                    $budgetInfo .= "، باقي {$remaining} ر.س.";
                } else {
                    $budgetInfo .= ' ⚠ تجاوزت الميزانية!';
                }
                $budgetInfo .= "\n\n";
            }

            return "📊 {$budgetInfo}إجمالي مصاريفك هذا الشهر: **{$totalExpenses} ر.س**\n\n"
                .($topCategory ? "أكبر فئة إنفاق: **{$topCategory->name}** ({$topCategory->total} ر.س)\n\n" : '')
                ."إجمالي إيراداتك: **{$totalIncome} ر.س**\n\n"
                .'الرصيد: **'.($totalIncome - $totalExpenses).' ر.س**';
        }

        if (str_contains($message, 'إيرادات') || str_contains($message, 'دخل') || str_contains($message, 'كم دخل')) {
            return "💰 إجمالي إيراداتك هذا الشهر: **{$totalIncome} ر.س**\n\n"
                .'صافى الرصيد (الإيرادات - المصاريف): **'.($totalIncome - $totalExpenses).' ر.س**';
        }

        if (str_contains($message, 'آخر') || str_contains($message, 'اخر') || str_contains($message, 'أحدث')) {
            $list = "📋 آخر معاملاتك:\n\n";
            foreach ($recentTransactions as $tx) {
                $emoji = $tx->type === 'income' ? '💰' : '💸';
                $sign = $tx->type === 'income' ? '+' : '-';
                $list .= "{$emoji} {$sign}{$tx->amount} ر.س - {$tx->description}";
                if ($tx->category) {
                    $list .= " ({$tx->category->name})";
                }
                $list .= " - {$tx->transaction_date->format('Y-m-d')}\n";
            }

            return $list;
        }

        if (str_contains($message, 'أين') || str_contains($message, 'وين') || str_contains($message, 'تقليل') || str_contains($message, 'توفير')) {
            $tips = "💡 نصائح للتوفير:\n\n";
            if ($topCategory && $topCategory->total > $totalExpenses * 0.3) {
                $tips .= "• فئة **{$topCategory->name}** تستحوذ على أكثر من 30% من مصاريفك - جرب تضع لها ميزانية محددة.\n";
            }
            $tips .= "• تتبع مصاريفك اليومية يساعدك تكتشف أين يذهب مالك بالضبط.\n";
            $tips .= "• حدد ميزانية شهرية من قسم الميزانية ومدبّر بينبهك إذا قربت تتجاوزها.\n";
            if ($totalExpenses > $totalIncome) {
                $tips .= "\n⚠️ مصاريفك أعلى من إيراداتك هذا الشهر - انتبه!";
            }

            return $tips;
        }

        if (str_contains($message, 'ميزانية') || str_contains($message, 'باقي')) {
            if ($budget && $budget->amount > 0) {
                $remaining = $budget->amount - $totalExpenses;
                $pct = round(($totalExpenses / $budget->amount) * 100);
                $status = $pct >= 100 ? '⚠️ تجاوزت الميزانية!' : ($pct >= 80 ? '🟠 اقتربت من الحد' : '🟢 وضعك ممتاز');

                return "📊 الميزانية الشهرية: **{$budget->amount} ر.س**\n"
                    ."تم الصرف: **{$totalExpenses} ر.س** ({$pct}%)\n"
                    ."المتبقي: **{$remaining} ر.س**\n"
                    ."الحالة: {$status}";
            }

            return 'لم تحدد ميزانية شهرية بعد. روح لقسم الميزانية وحدد ميزانيتك! 💪';
        }

        if (str_contains($message, 'شكراً') || str_contains($message, 'شكرا') || str_contains($message, 'تسلم')) {
            return 'عفواً! 😊 أنا في خدمتك. أي شي ثاني تحتاج تعرفه عن مصاريفك؟';
        }

        // Default response
        return "👋 أقدر أساعدك في:\n\n"
            ."📊 **كم صرفت هذا الشهر؟** - ملخص المصاريف\n"
            ."💰 **كم دخلي؟** - الإيرادات\n"
            ."📋 **آخر المعاملات** - أحدث العمليات\n"
            ."💡 **وين أقدر أوفر؟** - نصائح للتوفير\n"
            ."📊 **الميزانية** - حالة الميزانية\n\n"
            .'وش تبي تعرف؟';
    }
}
