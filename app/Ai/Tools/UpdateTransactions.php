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

class UpdateTransactions implements Tool
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
        return 'Update (edit) one or more existing transactions of the current user in a single call (bulk). '
            .'Use when the user asks to change the amount, description, category, type, payment method, date or notes of '
            .'an existing transaction. Each item needs an existing id (only the fields to change are required). '
            .'Call ListTransactions first to obtain exact ids; never guess an id. Up to 50 items per call. '
            .'A partial update succeeds even if some ids are not found (they are reported back).';
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
        $input = $request->all();

        if ($input === [] || ! isset($input['transactions']) || ! is_array($input['transactions'])) {
            return $this->error('المطلوب مصفوفة transactions تحتوي على عنصر واحد أو أكثر.');
        }

        $items = array_values($input['transactions']);

        if ($items === []) {
            return $this->error('لا يمكن تعديل صفر عمليات.');
        }

        if (count($items) > 50) {
            return $this->error('الحد الأقصى 50 عملية في الاستدعاء الواحد.');
        }

        $updates = [];
        $errors = [];

        foreach ($items as $index => $item) {
            if (! is_array($item) || ! isset($item['id']) || ! is_numeric($item['id'])) {
                $errors[$index] = ['id' => 'معرف العملية مطلوب ورقمي.'];

                continue;
            }

            $id = (int) $item['id'];

            $data = array_intersect_key($item, array_flip(['type', 'amount', 'description', 'category', 'payment_method', 'transaction_date', 'notes']));

            $categoryName = $data['category'] ?? null;
            unset($data['category']);

            [$categoryId, $categoryError] = Categories::resolve($this->user, is_string($categoryName) ? $categoryName : null);

            if ($categoryError !== null) {
                $errors[$index] = ['category' => $categoryError];

                continue;
            }

            if ($categoryId !== null) {
                $data['category_id'] = $categoryId;
            }

            if ($data === []) {
                $errors[$index] = ['_error' => 'لا توجد حقول للتعديل.'];

                continue;
            }

            $allowed = TransactionValidationRules::UPDATABLE_FIELDS;

            $data = array_intersect_key($data, array_flip($allowed));

            if ($data === []) {
                $errors[$index] = ['_error' => 'لا توجد حقول صالحة للتعديل.'];

                continue;
            }

            $validator = Validator::make($data, TransactionValidationRules::updateRules());

            if ($validator->fails()) {
                $errors[$index] = $validator->errors()->toArray();

                continue;
            }

            $updates[] = ['id' => $id, 'data' => $data];
        }

        if ($errors !== []) {
            return $this->error('فشل التعديل بسبب مدخلات غير صالحة.', ['errors' => $errors]);
        }

        [$updatedCount, $notFound] = DB::transaction(function () use ($updates): array {
            $updatedCount = 0;
            $notFound = [];

            foreach ($updates as $update) {
                $transaction = $this->user->transactions()->find($update['id']);

                if ($transaction === null) {
                    $notFound[] = $update['id'];

                    continue;
                }

                $transaction->forceFill($update['data'])->save();
                $updatedCount++;
            }

            return [$updatedCount, $notFound];
        });

        $summary = "عُدّلت {$updatedCount} عملية بنجاح.";

        if ($notFound !== []) {
            $summary .= ' لم تُعثر على '.count($notFound).' عملية (المعرّفات: '.implode('، ', $notFound).').';
        }

        return json_encode([
            'ok' => true,
            'summary' => $summary,
            'data' => [
                'updated_count' => $updatedCount,
                'not_found' => $notFound,
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
                    'id' => $schema->integer()->description('Existing transaction id (from ListTransactions).')->required(),
                    'type' => $schema->string()->enum(['expense', 'income'])->description('Transaction type.'),
                    'amount' => $schema->number()->min(0.01)->description('Amount in Saudi Riyal (SAR).'),
                    'description' => $schema->string()->description('Short description (max 255 chars). Nullable.'),
                    'category' => $schema->string()->description('Category name (must be one of the provided category list). Nullable to remove the category.'),
                    'payment_method' => $schema->string()->enum(['cash', 'credit_card', 'digital_wallet', 'bank_transfer'])->description('Payment method.'),
                    'transaction_date' => $schema->string()->description('Date in Y-m-d format.'),
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
