<?php

namespace App\Http\Requests\Supplier;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSupplierRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return true;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
   */
  public function rules(): array
  {
    return [
      'name' => 'sometimes|required|string|max:255',
      'email' => [
        'sometimes',
        'required',
        'email',
        Rule::unique('suppliers', 'email')->ignore($this->supplier),
      ],
      'phone' => 'nullable|string|max:50',
      'address' => 'nullable|string|max:500',
    ];
  }
}
