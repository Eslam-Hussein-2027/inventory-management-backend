<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
  use HasFactory;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'name',
    'description',
  ];

  /**
   * Get the products for the category.
   */
  public function products(): HasMany
  {
    return $this->hasMany(Product::class);
  }

  /**
   * Get the products count attribute.
   */
  public function getProductsCountAttribute(): int
  {
    return $this->products()->count();
  }
}
