<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Supplier\StoreSupplierRequest;
use App\Http\Requests\Supplier\UpdateSupplierRequest;
use App\Http\Resources\SupplierResource;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class SupplierController extends Controller
{
  /**
   * Display a listing of suppliers
   */
  public function index(): JsonResponse
  {
    $suppliers = QueryBuilder::for(Supplier::class)
      ->allowedFilters([
        AllowedFilter::partial('name'),
        AllowedFilter::exact('email'),
      ])
      ->allowedSorts(['name', 'email', 'created_at'])
      ->allowedIncludes(['products'])
      ->withCount('products')
      ->defaultSort('name')
      ->paginate(request('per_page', 15))
      ->appends(request()->query());

    return $this->success(SupplierResource::collection($suppliers)->response()->getData(true));
  }

  /**
   * Store a newly created supplier
   */
  public function store(StoreSupplierRequest $request): JsonResponse
  {
    $supplier = Supplier::create($request->validated());

    return $this->created(new SupplierResource($supplier), 'Supplier created successfully');
  }

  /**
   * Display the specified supplier
   */
  public function show(Supplier $supplier): JsonResponse
  {
    $supplier = QueryBuilder::for(Supplier::where('id', $supplier->id))
      ->allowedIncludes(['products'])
      ->withCount('products')
      ->firstOrFail();

    return $this->success(new SupplierResource($supplier));
  }

  /**
   * Update the specified supplier
   */
  public function update(UpdateSupplierRequest $request, Supplier $supplier): JsonResponse
  {
    $supplier->update($request->validated());

    return $this->success(new SupplierResource($supplier->fresh()), 'Supplier updated successfully');
  }

  /**
   * Remove the specified supplier
   */
  public function destroy(Supplier $supplier): JsonResponse
  {
    $supplier->delete();

    return $this->noContent('Supplier deleted successfully');
  }
}
