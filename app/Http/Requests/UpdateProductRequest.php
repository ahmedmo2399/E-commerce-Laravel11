<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;  // You can modify this if you want to check permissions or roles.
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $productId = $this->route('id'); 

        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|unique:products,slug,' . $productId,
            'short_description' => 'required|string',
            'description' => 'required|string',
            'regular_price' => 'required|numeric',
            'sale_price' => 'required|numeric|lte:regular_price',
            'SKU' => 'required|string',
            'stock_status' => 'required|string',
            'featured' => 'required|boolean',
            'quantity' => 'required|integer|min:1',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048|dimensions:min_width=300,min_height=300',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The product name is required.',
            'short_description.required' => 'Please provide a short description.',
            'description.required' => 'The product description is required.',
            'regular_price.required' => 'The regular price is required.',
            'sale_price.lte' => 'The sale price must be less than or equal to the regular price.',
            'category_id.exists' => 'The selected category does not exist.',
            'brand_id.exists' => 'The selected brand does not exist.',
            'image.dimensions' => 'The image must be at least 300px by 300px.',
        ];
    }
}
