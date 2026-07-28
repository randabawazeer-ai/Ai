<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class TransactionController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Transaction::where('user_id', $request->user()->id)
            ->with('category')
            ->latest('transaction_date');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('search')) {
            $query->where('description', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('transaction_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('transaction_date', '<=', $request->date_to);
        }

        $transactions = $query->paginate(20)->withQueryString();

        $categories = Category::whereNull('user_id')
            ->orWhere('user_id', $request->user()->id)
            ->get();

        return Inertia::render('transactions/Index', [
            'transactions' => $transactions,
            'categories' => $categories,
            'filters' => $request->only(['type', 'category_id', 'payment_method', 'search', 'date_from', 'date_to']),
        ]);
    }

    public function create(Request $request): Response
    {
        $categories = Category::whereNull('user_id')
            ->orWhere('user_id', $request->user()->id)
            ->get();

        return Inertia::render('transactions/Create', [
            'categories' => $categories,
        ]);
    }

    public function store(TransactionRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;

        if ($request->hasFile('receipt_image')) {
            $data['receipt_image_path'] = $request->file('receipt_image')->store('receipts', 'public');
        }
        unset($data['receipt_image']);

        Transaction::create($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'تمت إضافة المعاملة بنجاح']);

        return to_route('transactions.index');
    }

    public function edit(Request $request, Transaction $transaction): Response
    {
        if ($transaction->user_id !== $request->user()->id) {
            abort(403);
        }

        $categories = Category::whereNull('user_id')
            ->orWhere('user_id', $request->user()->id)
            ->get();

        return Inertia::render('transactions/Edit', [
            'transaction' => $transaction->load('category'),
            'categories' => $categories,
        ]);
    }

    public function update(TransactionRequest $request, Transaction $transaction): RedirectResponse
    {
        if ($transaction->user_id !== $request->user()->id) {
            abort(403);
        }

        $data = $request->validated();

        if ($request->hasFile('receipt_image')) {
            if ($transaction->receipt_image_path) {
                Storage::disk('public')->delete($transaction->receipt_image_path);
            }
            $data['receipt_image_path'] = $request->file('receipt_image')->store('receipts', 'public');
        }
        unset($data['receipt_image']);

        $transaction->update($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'تم تحديث المعاملة بنجاح']);

        return to_route('transactions.index');
    }

    public function destroy(Request $request, Transaction $transaction): RedirectResponse
    {
        if ($transaction->user_id !== $request->user()->id) {
            abort(403);
        }

        if ($transaction->receipt_image_path) {
            Storage::disk('public')->delete($transaction->receipt_image_path);
        }

        $transaction->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'تم حذف المعاملة بنجاح']);

        return to_route('transactions.index');
    }
}
