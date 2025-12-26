<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @return array<string, mixed>
   */
  public function toArray(Request $request): array
  {
    return [
      'id' => (string) $this->id,
      'name' => $this->name,
      'email' => $this->email,
      'role' => $this->roles->first()?->name ?? 'user',
      'avatar' => $this->getFirstMediaUrl('avatar'),
      'createdAt' => $this->created_at->toISOString(),
      'updatedAt' => $this->updated_at->toISOString(),
    ];
  }
}
