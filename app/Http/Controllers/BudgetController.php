<?php

namespace App\Http\Controllers;

use App\Http\Requests\BudgetRequest;
use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BudgetController extends Controller
{
    public function index(Request $request): Response
    {
        $userId = $request->user()->id;
        $currentMonth = now()->format('Y-m');

        $month = $request->get('month', $currentMonth);

        $budgets = Budget::where('user_id', $userId)
            ->where('month', $month)
            ->with('category')
            ->get();

        $categories = Category::whereNull('user_id')
            ->orWhere('user_id', $userId)
            ->whereIn('type', ['expense', 'both'])
            ->get();

        $spending = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereRaw("strftime('%Y-%m', transaction_date) = ?", [$month])
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->pluck('total', 'category_id');

        return Inertia::render('budgets/Index', [
            'budgets' => $budgets,
            'categories' => $categories,
            'month' => $month,
            'spending' => $spending,
        ]);
    }

    public function store(BudgetRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;

        Budget::updateOrCreate(
            [
                'user_id' => $data['user_id'],
                'category_id' => $data['category_id'],
                'month' => $data['month'],
            ],
            ['amount' => $data['amount']]
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => 'تم حفظ الميزانية بنجاح']);

        return to_route('budgets.index', ['month' => $data['month']]);
    }

    public function destroy(Request $request, Budget $budget): RedirectResponse
    {
        if ($budget->user_id !== $request->user()->id) {
            abort(403);
        }

        $budget->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'تم حذف الميزانية بنجاح']);

        return to_route('budgets.index');
    }
}
