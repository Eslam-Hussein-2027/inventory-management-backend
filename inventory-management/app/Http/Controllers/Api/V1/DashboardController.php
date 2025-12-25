<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\User;
use App\Enums\OrderStatus;
use App\Http\Resources\OrderResource;
use App\Http\Resources\ProductResource;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function stats(): JsonResponse
    {
        $stats = [
            'totalProducts' => Product::count(),
            'totalCategories' => Category::count(),
            'totalOrders' => Order::count(),
            'totalUsers' => User::count(),
            'lowStockCount' => Product::lowStock(10)->count(),
            'pendingOrders' => Order::where('status', OrderStatus::PENDING)->count(),
            'totalRevenue' => Order::where('status', OrderStatus::COMPLETED)->sum('total_price'),
            'recentOrders' => OrderResource::collection(
                Order::with(['user', 'items.product'])
                    ->latest()
                    ->limit(5)
                    ->get()
            ),
            'topProducts' => ProductResource::collection(
                Product::withCount('orderItems')
                    ->orderByDesc('order_items_count')
                    ->limit(5)
                    ->get()
            ),
            'lowStockProducts' => ProductResource::collection(
                Product::lowStock(10)
                    ->with('category')
                    ->limit(5)
                    ->get()
            ),
        ];

        return $this->success($stats);
    }
}
