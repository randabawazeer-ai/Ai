<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    public function rules(): array
    {
        $categoryId = $this->route('category')?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:50'],
            'type' => ['required', 'in:expense,income,both'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم التصنيف مطلوب',
            'name.max' => 'اسم التصنيف يجب ألا يتجاوز 255 حرفاً',
            'type.required' => 'نوع التصنيف مطلوب',
            'type.in' => 'نوع التصنيف غير صالح',
        ];
    }
}
