<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return $this->user()->hasPermissionTo('users.update', 'sanctum');
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
      'email' => ['sometimes', 'email', Rule::unique('users')->ignore($this->route('user'))],
      'password' => ['sometimes', 'string', 'min:8'],
      'role' => ['sometimes', 'string', 'in:admin,user'],
      'avatar' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:2048'],
    ];
  }
}
