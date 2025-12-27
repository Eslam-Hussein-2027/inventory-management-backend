<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;

class ProductController extends Controller
{
  /**
   * Display a listing of products with filtering, sorting, and including relations
   * 
   * Example queries:
   * GET /api/v1/products?filter[name]=laptop&filter[category_id]=1&sort=-price&include=category
   * GET /api/v1/products?filter[price_min]=100&filter[price_max]=500&sort=name
   */
  public function index(): JsonResponse
  {
    $products = QueryBuilder::for(Product::class)
      ->allowedFilters([
        AllowedFilter::exact('category_id'),
        AllowedFilter::partial('name'),
        AllowedFilter::partial('sku'),
        AllowedFilter::scope('low_stock'),
        AllowedFilter::callback('price_min', fn($query, $value) => $query->where('price', '>=', $value)),
        AllowedFilter::callback('price_max', fn($query, $value) => $query->where('price', '<=', $value)),
      ])
      ->allowedSorts([
        'name',
        'price',
        'quantity',
        'created_at',
        AllowedSort::field('newest', 'created_at'),
      ])
      ->allowedIncludes(['category', 'supplier'])
      ->with(['category', 'supplier'])
      ->defaultSort('-created_at')
      ->paginate(request('per_page', 15))
      ->appends(request()->query());

    return $this->success(ProductResource::collection($products)->response()->getData(true));
  }

  /**
   * Store a newly created product
   */
  public function store(StoreProductRequest $request): JsonResponse
  {
    $product = Product::create($request->validated());

    // Handle image upload with Media Library
    if ($request->hasFile('image')) {
      $product->addMediaFromRequest('image')
        ->toMediaCollection('product_image');
    }

    return $this->created(
      new ProductResource($product->load(['category', 'supplier'])),
      'Product created successfully'
    );
  }

  /**
   * Display the specified product
   */
  public function show(Product $product): JsonResponse
  {
    $product = QueryBuilder::for(Product::where('id', $product->id))
      ->allowedIncludes(['category', 'supplier'])
      ->with(['category', 'supplier'])
      ->firstOrFail();

    return $this->success(new ProductResource($product));
  }

  /**
   * Update the specified product
   */
  public function update(UpdateProductRequest $request, Product $product): JsonResponse
  {
    $product->update($request->validated());

    // Handle image upload with Media Library
    if ($request->hasFile('image')) {
      $product->clearMediaCollection('product_image');
      $product->addMediaFromRequest('image')
        ->toMediaCollection('product_image');
    }

    return $this->success(
      new ProductResource($product->fresh()->load(['category', 'supplier'])),
      'Product updated successfully'
    );
  }

  /**
   * Remove the specified product
   */
  public function destroy(Product $product): JsonResponse
  {
    $product->delete();

    return $this->noContent('Product deleted successfully');
  }

  /**
   * Get low stock products
   */
  public function lowStock(): JsonResponse
  {
    $products = Product::lowStock(10)
      ->with('category')
      ->orderBy('quantity')
      ->get();

    return $this->success(ProductResource::collection($products));
  }

  /**
   * Get best selling products
   */
  public function bestSelling(): JsonResponse
  {
    $products = Product::withCount('orderItems')
      ->orderByDesc('order_items_count')
      ->limit(10)
      ->get();

    return $this->success(ProductResource::collection($products));
  }
}
