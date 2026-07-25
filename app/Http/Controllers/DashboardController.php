<?php

namespace App\Http\Controllers;

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

        return Inertia::render('Dashboard', [
            'stats' => [
                'total_expenses' => (float) $totalExpenses,
                'total_income' => (float) $totalIncome,
                'balance' => (float) ($totalIncome - $totalExpenses),
                'transaction_count' => $transactionCount,
            ],
            'recentTransactions' => $recentTransactions,
        ]);
    }
}
