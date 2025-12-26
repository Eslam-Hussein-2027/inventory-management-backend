<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return $this->user()->hasPermissionTo('products.create', 'sanctum');
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
   */
  public function rules(): array
  {
    return [
      'name' => ['required', 'string', 'max:255'],
      'sku' => ['required', 'string', 'unique:products'],
      'description' => ['nullable', 'string'],
      'price' => ['required', 'numeric', 'min:0'],
      'quantity' => ['required', 'integer', 'min:0'],
      'category_id' => ['required', 'exists:categories,id'],
      'supplier_id' => ['nullable', 'exists:suppliers,id'],
      'image' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:2048'],
    ];
  }
}
