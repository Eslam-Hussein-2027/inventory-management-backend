<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
      'sku' => $this->sku,
      'description' => $this->description,
      'price' => (float) $this->price,
      'quantity' => (int) $this->quantity,
      'categoryId' => (string) $this->category_id,
      'category' => new CategoryResource($this->whenLoaded('category')),
      'supplierId' => $this->supplier_id ? (string) $this->supplier_id : null,
      'supplier' => new SupplierResource($this->whenLoaded('supplier')),
      'imageUrl' => $this->image_url,
      'thumbUrl' => $this->thumb_url,
      'createdAt' => $this->created_at->toISOString(),
      'updatedAt' => $this->updated_at->toISOString(),
    ];
  }
}
