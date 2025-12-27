<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends Model implements HasMedia
{
  use HasFactory, InteractsWithMedia;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'name',
    'sku',
    'description',
    'price',
    'quantity',
    'category_id',
    'supplier_id',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [
    'price' => 'decimal:2',
    'quantity' => 'integer',
  ];

  /**
   * Get the category that owns the product.
   */
  public function category(): BelongsTo
  {
    return $this->belongsTo(Category::class);
  }

  /**
   * Get the supplier that owns the product.
   */
  public function supplier(): BelongsTo
  {
    return $this->belongsTo(Supplier::class);
  }

  /**
   * Get the order items for the product.
   */
  public function orderItems(): HasMany
  {
    return $this->hasMany(OrderItem::class);
  }

  /**
   * Register media collections for the product.
   */
  public function registerMediaCollections(): void
  {
    $this->addMediaCollection('product_image')
      ->singleFile()
      ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
  }

  /**
   * Register media conversions for the product.
   */
  public function registerMediaConversions(?Media $media = null): void
  {
    $this->addMediaConversion('thumb')
      ->width(150)
      ->height(150)
      ->sharpen(10);

    $this->addMediaConversion('preview')
      ->width(400)
      ->height(400);
  }

  /**
   * Scope a query to only include low stock products.
   */
  public function scopeLowStock($query, int $threshold = 10)
  {
    return $query->where('quantity', '<=', $threshold);
  }

  /**
   * Get the image URL attribute.
   */
  public function getImageUrlAttribute(): ?string
  {
    return $this->getFirstMediaUrl('product_image', 'preview') ?: null;
  }

  /**
   * Get the thumbnail URL attribute.
   */
  public function getThumbUrlAttribute(): ?string
  {
    return $this->getFirstMediaUrl('product_image', 'thumb') ?: null;
  }
}
