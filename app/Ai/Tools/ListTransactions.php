<?php

namespace App\Ai\Tools;

use App\Ai\Support\Categories;
use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Validator;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class ListTransactions implements Tool
{
    public function __construct(protected User $user)
    {
        //
    }

    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
        return 'List the current user transactions with optional filters (date range, type, category, amount range, search, sort). '
            .'Use this to answer questions about spending, compute totals for a category/period, find exact transaction IDs, '
            .'or inspect records BEFORE updating/deleting them. Always call this tool to obtain real data; never invent amounts or IDs. '
            .'NOT for creating, updating or deleting transactions.';
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
        $validator = Validator::make($request->all(), [
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
            'type' => ['nullable', 'in:expense,income'],
            'category' => ['nullable', 'string'],
            'min_amount' => ['nullable', 'numeric', 'min:0'],
            'max_amount' => ['nullable', 'numeric', 'min:0'],
            'search' => ['nullable', 'string', 'max:255'],
            'sort' => ['nullable', 'in:date_desc,date_asc,amount_desc,amount_asc'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }

        $data = $request->all();
        $limit = min((int) ($data['limit'] ?? 25), 100);

        $query = $this->user->transactions()
            ->with('category')
            ->whereNull('family_id');

        if (filled($data['date_from'] ?? null)) {
            $query->whereDate('transaction_date', '>=', $data['date_from']);
        }

        if (filled($data['date_to'] ?? null)) {
            $query->whereDate('transaction_date', '<=', $data['date_to']);
        }

        if (filled($data['type'] ?? null)) {
            $query->where('type', $data['type']);
        }

        if (filled($data['category'] ?? null)) {
            [$categoryId, $error] = Categories::resolve($this->user, $data['category']);

            if ($error) {
                return $this->error($error);
            }

            $query->where('category_id', $categoryId);
        }

        if (filled($data['min_amount'] ?? null)) {
            $query->where('amount', '>=', $data['min_amount']);
        }

        if (filled($data['max_amount'] ?? null)) {
            $query->where('amount', '<=', $data['max_amount']);
        }

        if (filled($data['search'] ?? null)) {
            $needle = $data['search'];
            $query->where(fn ($q) => $q->where('description', 'like', "%{$needle}%")->orWhere('notes', 'like', "%{$needle}%"));
        }

        $totalCount = (clone $query)->count();
        $sumAmount = (float) (clone $query)->sum('amount');

        [$sortColumn, $sortDirection] = match ($data['sort'] ?? 'date_desc') {
            'date_asc' => ['transaction_date', 'asc'],
            'amount_desc' => ['amount', 'desc'],
            'amount_asc' => ['amount', 'asc'],
            default => ['transaction_date', 'desc'],
        };

        $query->orderBy($sortColumn, $sortDirection);

        $transactions = $query->limit($limit)->get()->map(fn ($tx): array => [
            'id' => $tx->id,
            'date' => $tx->transaction_date->format('Y-m-d'),
            'type' => $tx->type,
            'category' => $tx->category?->name,
            'amount' => (float) $tx->amount,
            'description' => $tx->description,
        ])->all();

        $truncated = $totalCount > $limit;

        return json_encode([
            'ok' => true,
            'summary' => "وجَد {$totalCount} عملية (إجمالي المبلغ {$sumAmount} ر.س)."
                .($truncated ? ' النتائج تتجاوز حد العرض، أُعيد أحدثها فقط.' : ''),
            'data' => [
                'transactions' => $transactions,
                'total_count' => $totalCount,
                'sum_amount' => $sumAmount,
                'truncated' => $truncated,
            ],
        ], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Get the tool's schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'date_from' => $schema->string()->format('date')->description('Start date (Y-m-d). Optional.'),
            'date_to' => $schema->string()->format('date')->description('End date (Y-m-d). Optional.'),
            'type' => $schema->string()->enum(['expense', 'income'])->description('Filter by transaction type.'),
            'category' => $schema->string()->description('Filter by category name (from the provided category list).'),
            'min_amount' => $schema->number()->min(0)->description('Minimum amount.'),
            'max_amount' => $schema->number()->min(0)->description('Maximum amount.'),
            'search' => $schema->string()->description('Free text search in description / notes.'),
            'sort' => $schema->string()->enum(['date_desc', 'date_asc', 'amount_desc', 'amount_asc'])->description('Sort order. Default: date_desc.'),
            'limit' => $schema->integer()->min(1)->max(100)->description('Max number of transactions to return. Default 25, max 100.'),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function error(string $message, array $data = []): string
    {
        return json_encode([
            'ok' => false,
            'summary' => $message,
            'data' => $data,
        ], JSON_UNESCAPED_UNICODE);
    }
}
