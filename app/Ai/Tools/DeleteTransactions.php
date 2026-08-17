<?php

namespace App\Ai\Tools;

use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\DB;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class DeleteTransactions implements Tool
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
        return 'Delete one or more existing transactions of the current user in a single call (bulk). '
            .'Use when the user asks to remove/delete a transaction. Each item needs an existing id '
            .'(call ListTransactions first to obtain exact ids; never guess an id). Up to 50 items per call. '
            .'Deletion is permanent. A partial delete succeeds even if some ids are not found (they are reported back).';
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
        $input = $request->all();

        if ($input === [] || ! isset($input['ids']) || ! is_array($input['ids']) || $input['ids'] === []) {
            return $this->error('المطلوب مصفوفة ids تحتوي على معرف واحد أو أكثر.');
        }

        $ids = array_values(array_filter(array_map(fn ($id) => is_numeric($id) ? (int) $id : null, $input['ids'])));

        if ($ids === []) {
            return $this->error('معرّفات العمليات يجب أن تكون رقمية.');
        }

        if (count($ids) > 50) {
            return $this->error('الحد الأقصى 50 عملية في الاستدعاء الواحد.');
        }

        [$deletedIds, $notFound] = DB::transaction(function () use ($ids): array {
            $deletedIds = [];
            $notFound = [];

            foreach ($ids as $id) {
                $transaction = $this->user->transactions()->find($id);

                if ($transaction === null) {
                    $notFound[] = $id;

                    continue;
                }

                $transaction->delete();
                $deletedIds[] = $id;
            }

            return [$deletedIds, $notFound];
        });

        $summary = 'حُذفت '.count($deletedIds).' عملية بنجاح.';

        if ($notFound !== []) {
            $summary .= ' لم تُعثر على '.count($notFound).' عملية (المعرّفات: '.implode('، ', $notFound).').';
        }

        return json_encode([
            'ok' => true,
            'summary' => $summary,
            'data' => [
                'deleted_ids' => $deletedIds,
                'deleted_count' => count($deletedIds),
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
            'ids' => $schema->array()
                ->min(1)
                ->max(50)
                ->items($schema->integer())
                ->required()
                ->description('Array of transaction ids to delete (from ListTransactions).'),
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
