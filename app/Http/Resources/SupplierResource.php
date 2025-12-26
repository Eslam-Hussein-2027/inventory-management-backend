<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplierResource extends JsonResource
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
      'phone' => $this->phone,
      'address' => $this->address,
      'productsCount' => $this->whenCounted('products'),
      'products' => ProductResource::collection($this->whenLoaded('products')),
      'createdAt' => $this->created_at->toISOString(),
      'updatedAt' => $this->updated_at->toISOString(),
    ];
  }
}
