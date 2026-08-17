<?php

namespace App\Ai\Tools;

use App\Ai\Support\Categories;
use App\Ai\Support\TransactionValidationRules;
use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class CreateTransactions implements Tool
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
        return 'Create (add) one or more transactions for the current user in a single call (bulk). '
            .'Use when the user asks to add/record a transaction or income. '
            .'Every item requires: type (expense|income), amount (>0, in SAR), payment_method, transaction_date (Y-m-d). '
            .'description, category (a name from the provided category list) and notes are optional. '
            .'Ask the user for any required field that is missing instead of guessing. Up to 50 items per call. '
            .'Returns the created transaction IDs.';
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
        $items = $request->all();

        if ($items === [] || ! isset($items['transactions']) || ! is_array($items['transactions'])) {
            return $this->error('المطلوب مصفوفة transactions تحتوي على عنصر واحد أو أكثر.');
        }

        $items = array_values($items['transactions']);

        if ($items === []) {
            return $this->error('لا يمكن إضافة صفر عمليات.');
        }

        if (count($items) > 50) {
            return $this->error('الحد الأقصى 50 عملية في الاستدعاء الواحد.');
        }

        $rows = [];
        $errors = [];

        foreach ($items as $index => $item) {
            if (! is_array($item)) {
                $errors[$index] = ['_error' => 'البيانات غير صالحة'];

                continue;
            }

            $data = array_intersect_key($item, array_flip(['type', 'amount', 'description', 'payment_method', 'transaction_date', 'notes']));

            $categoryName = $item['category'] ?? null;
            [$categoryId, $categoryError] = Categories::resolve($this->user, is_string($categoryName) ? $categoryName : null);

            if ($categoryError !== null) {
                $errors[$index] = ['category' => $categoryError];

                continue;
            }

            if ($categoryId !== null) {
                $data['category_id'] = $categoryId;
            }

            $validator = Validator::make($data, TransactionValidationRules::rules());

            if ($validator->fails()) {
                $errors[$index] = $validator->errors()->toArray();

                continue;
            }

            $rows[] = $data;
        }

        if ($errors !== []) {
            return $this->error('فشل إضافة العمليات بسبب مدخلات غير صالحة.', ['errors' => $errors]);
        }

        $ids = DB::transaction(function () use ($rows): array {
            $ids = [];

            foreach ($rows as $row) {
                $transaction = $this->user->transactions()->create($row);
                $ids[] = $transaction->id;
            }

            return $ids;
        });

        return json_encode([
            'ok' => true,
            'summary' => 'أُضيفت '.count($ids).' عملية بنجاح.',
            'data' => [
                'ids' => $ids,
                'count' => count($ids),
            ],
        ], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Get the tool's schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'transactions' => $schema->array()
                ->min(1)
                ->max(50)
                ->items($schema->object(fn ($schema): array => [
                    'type' => $schema->string()->enum(['expense', 'income'])->description('Transaction type.')->required(),
                    'amount' => $schema->number()->min(0.01)->description('Amount in Saudi Riyal (SAR).')->required(),
                    'description' => $schema->string()->description('Short description (max 255 chars).'),
                    'category' => $schema->string()->description('Category name (must be one of the provided category list).'),
                    'payment_method' => $schema->string()->enum(['cash', 'credit_card', 'digital_wallet', 'bank_transfer'])->description('Payment method.')->required(),
                    'transaction_date' => $schema->string()->description('Date in Y-m-d format.')->required(),
                    'notes' => $schema->string()->description('Optional notes (max 1000 chars).'),
                ]))
                ->required(),
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
