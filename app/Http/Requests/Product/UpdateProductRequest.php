<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return $this->user()->hasPermissionTo('products.update', 'sanctum');
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
   */
  public function rules(): array
  {
    return [
      'name' => ['sometimes', 'string', 'max:255'],
      'sku' => ['sometimes', 'string', Rule::unique('products')->ignore($this->product)],
      'description' => ['nullable', 'string'],
      'price' => ['sometimes', 'numeric', 'min:0'],
      'quantity' => ['sometimes', 'integer', 'min:0'],
      'category_id' => ['sometimes', 'exists:categories,id'],
      'supplier_id' => ['nullable', 'exists:suppliers,id'],
      'image' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:2048'],
    ];
  }
}
