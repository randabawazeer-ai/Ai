<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'type' => ['required', 'in:expense,income'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:999999999.99'],
            'description' => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'payment_method' => ['required', 'in:cash,credit_card,digital_wallet,bank_transfer'],
            'transaction_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'receipt_image' => ['nullable', 'image', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'نوع المعاملة مطلوب',
            'type.in' => 'نوع المعاملة غير صالح',
            'amount.required' => 'المبلغ مطلوب',
            'amount.numeric' => 'المبلغ يجب أن يكون رقماً',
            'amount.min' => 'المبلغ يجب أن يكون أكبر من صفر',
            'payment_method.required' => 'طريقة الدفع مطلوبة',
            'payment_method.in' => 'طريقة الدفع غير صالحة',
            'transaction_date.required' => 'التاريخ مطلوب',
            'transaction_date.date' => 'التاريخ غير صالح',
            'receipt_image.image' => 'الملف يجب أن يكون صورة',
            'receipt_image.max' => 'حجم الصورة يجب ألا يتجاوز 5 ميجابايت',
        ];
    }
}
