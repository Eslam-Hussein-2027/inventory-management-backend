<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
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
      'productId' => (string) $this->product_id,
      'product' => new ProductResource($this->whenLoaded('product')),
      'quantity' => (int) $this->quantity,
      'price' => (float) $this->price,
      'subtotal' => (float) $this->subtotal,
    ];
  }
}
