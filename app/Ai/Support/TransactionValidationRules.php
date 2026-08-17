<?php

namespace App\Ai\Support;

/**
 * Single source of truth for transaction validation rules.
 *
 * `TransactionRequest` and the AI finance tools resolve their rules from
 * here so they can never drift apart.
 */
class TransactionValidationRules
{
    public const TYPES = ['expense', 'income'];

    public const PAYMENT_METHODS = ['cash', 'credit_card', 'digital_wallet', 'bank_transfer'];

    /**
     * Fields that may be updated on an existing transaction.
     *
     * @var string[]
     */
    public const UPDATABLE_FIELDS = ['type', 'amount', 'description', 'category_id', 'payment_method', 'transaction_date', 'notes'];

    /**
     * Get the base transaction validation rules.
     *
     * @return array<string, mixed>
     */
    public static function rules(): array
    {
        return [
            'type' => ['required', 'in:expense,income'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:999999999.99'],
            'description' => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'payment_method' => ['required', 'in:cash,credit_card,digital_wallet,bank_transfer'],
            'transaction_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get the validation rules for a single field.
     *
     * @return mixed[]
     */
    public static function fieldRules(string $field): array
    {
        return self::rules()[$field] ?? [];
    }

    /**
     * Get the validation rules for updating an existing transaction.
     *
     * Every updatable field validates against the same constraint set when
     * present, so an update form can never introduce a format the create
     * route would reject.
     *
     * @return array<string, mixed>
     */
    public static function updateRules(): array
    {
        return array_intersect_key(self::rules(), array_flip(self::UPDATABLE_FIELDS));
    }
}
