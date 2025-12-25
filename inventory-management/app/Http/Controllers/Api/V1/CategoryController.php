<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class CategoryController extends Controller
{
  
    public function index(): JsonResponse
    {
        $categories = QueryBuilder::for(Category::class)
            ->allowedFilters([
                AllowedFilter::partial('name'),
            ])
            ->allowedSorts(['name', 'created_at'])
            ->allowedIncludes(['products'])
            ->withCount('products')
            ->defaultSort('name')
            ->paginate(request('per_page', 15))
            ->appends(request()->query());

        return $this->success(CategoryResource::collection($categories)->response()->getData(true));
    }
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = Category::create($request->validated());

        return $this->created(new CategoryResource($category), 'Category created successfully');
    }
    public function show(Category $category): JsonResponse
    {
        $category = QueryBuilder::for(Category::where('id', $category->id))
            ->allowedIncludes(['products'])
            ->withCount('products')
            ->firstOrFail();

        return $this->success(new CategoryResource($category));
    }
    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $category->update($request->validated());

        return $this->success(new CategoryResource($category->fresh()), 'Category updated successfully');
    }
    public function destroy(Category $category): JsonResponse
    {
        $category->delete();

        return $this->noContent('Category deleted successfully');
    }
}
