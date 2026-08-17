<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $userId = $request->user()->id;
        $currentMonth = now()->startOfMonth();
        $currentMonthEnd = now()->endOfMonth();

        $monthTransactions = Transaction::where('user_id', $userId)
            ->whereBetween('transaction_date', [$currentMonth, $currentMonthEnd]);

        $totalExpenses = (clone $monthTransactions)->where('type', 'expense')->sum('amount');
        $totalIncome = (clone $monthTransactions)->where('type', 'income')->sum('amount');
        $transactionCount = (clone $monthTransactions)->count();

        $recentTransactions = Transaction::where('user_id', $userId)
            ->with('category')
            ->latest('transaction_date')
            ->limit(10)
            ->get();

        $monthlyExpenses = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->where('transaction_date', '>=', now()->subMonths(5)->startOfMonth())
            ->get(['transaction_date', 'amount'])
            ->groupBy(fn (Transaction $transaction) => $transaction->transaction_date->format('Y-m'))
            ->map(fn ($transactions) => (float) $transactions->sum('amount'))
            ->all();

        $lastSixMonths = collect(range(5, 0))
            ->map(fn (int $i) => now()->subMonths($i)->format('Y-m'))
            ->map(fn (string $month) => (float) ($monthlyExpenses[$month] ?? 0))
            ->values()
            ->all();

        $currentMonthStr = now()->format('Y-m');
        $totalBudget = Budget::where('user_id', $userId)
            ->where('month', $currentMonthStr)
            ->sum('amount');

        return Inertia::render('Dashboard', [
            'stats' => [
                'total_expenses' => (float) $totalExpenses,
                'total_income' => (float) $totalIncome,
                'balance' => $totalBudget > 0 ? (float) ($totalBudget - $totalExpenses) : null,
                'transaction_count' => $transactionCount,
                'total_budget' => (float) $totalBudget,
                'budget_percent' => $totalBudget > 0 ? min(100, ($totalExpenses / $totalBudget) * 100) : 0,
            ],
            'recentTransactions' => $recentTransactions,
            'monthlyExpenses' => $lastSixMonths,
        ]);
    }
}
