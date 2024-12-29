<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    /**
     * تحديد صلاحية المستخدم لاستخدام هذا الطلب.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * قواعد التحقق الخاصة بالطلب.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|unique:categories,slug,' . $this->id,
            'image' => 'nullable|mimes:png,jpg,jpeg|max:2048',
        ];
    }


    public function messages(): array
    {
        return [
            'name.required' => 'اسم الفئة مطلوب.',
            'slug.required' => 'السجل مطلوب.',
            'slug.unique' => 'السجل مستخدم بالفعل.',
            'image.mimes' => 'يجب أن تكون الصورة بصيغة PNG, JPG, أو JPEG.',
            'image.max' => 'يجب ألا يزيد حجم الصورة عن 2 ميجابايت.',
        ];
    }
}
