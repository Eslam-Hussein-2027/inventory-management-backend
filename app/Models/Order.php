<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\OrderStatus;

class Order extends Model
{
  use HasFactory;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'user_id',
    'total_price',
    'status',
    'order_date',
    'address',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [
    'total_price' => 'decimal:2',
    'status' => OrderStatus::class,
    'order_date' => 'datetime',
  ];

  /**
   * Get the user that owns the order.
   */
  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  /**
   * Get the order items for the order.
   */
  public function items(): HasMany
  {
    return $this->hasMany(OrderItem::class);
  }

  /**
   * Calculate the total from items.
   */
  public function calculateTotal(): float
  {
    return $this->items->sum(fn($item) => $item->price * $item->quantity);
  }
}
