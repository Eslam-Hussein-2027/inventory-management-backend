<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Requests\Order\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Product;
use App\Enums\OrderStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class OrderController extends Controller
{
    public function index(): JsonResponse
    {
        $orders = QueryBuilder::for(Order::class)
            ->allowedFilters([
                AllowedFilter::exact('status'),
                AllowedFilter::exact('user_id'),
                AllowedFilter::callback('date_from', fn($query, $value) => $query->whereDate('order_date', '>=', $value)),
                AllowedFilter::callback('date_to', fn($query, $value) => $query->whereDate('order_date', '<=', $value)),
            ])
            ->allowedSorts(['order_date', 'total_price', 'status', 'created_at'])
            ->allowedIncludes(['user', 'items', 'items.product'])
            ->with(['user', 'items.product'])
            ->defaultSort('-created_at')
            ->paginate(request('per_page', 15))
            ->appends(request()->query());

        return $this->success(OrderResource::collection($orders)->response()->getData(true));
    }

    /**
     * Get current user's orders
     */
    public function myOrders(Request $request): JsonResponse
    {
        $orders = QueryBuilder::for(Order::where('user_id', $request->user()->id))
            ->allowedFilters([
                AllowedFilter::exact('status'),
            ])
            ->allowedSorts(['order_date', 'total_price', 'created_at'])
            ->with(['items.product'])
            ->defaultSort('-created_at')
            ->paginate(request('per_page', 15))
            ->appends(request()->query());

        return $this->success(OrderResource::collection($orders)->response()->getData(true));
    }

    /**
     * Store a newly created order
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        $order = DB::transaction(function () use ($request) {
            $order = Order::create([
                'user_id' => $request->user()->id,
                'total_price' => 0,
                'status' => OrderStatus::PENDING,
                'order_date' => now(),
            ]);

            $totalPrice = 0;

            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);

                // Check stock
                if ($product->quantity < $item['quantity']) {
                    throw new \Exception("Insufficient stock for product: {$product->name}");
                }

                // Create order item
                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                ]);

                // Update product stock
                $product->decrement('quantity', $item['quantity']);

                $totalPrice += $product->price * $item['quantity'];
            }

            $order->update(['total_price' => $totalPrice]);

            return $order;
        });

        return $this->created(
            new OrderResource($order->load(['items.product', 'user'])),
            'Order created successfully'
        );
    }

    /**
     * Display the specified order
     */
    public function show(Order $order): JsonResponse
    {
        // Check if user can view this order
        if (!request()->user()->hasRole('admin') && $order->user_id !== request()->user()->id) {
            return $this->error('Unauthorized', 403);
        }

        return $this->success(new OrderResource($order->load(['items.product', 'user'])));
    }

    /**
     * Update order status (Admin only)
     */
    public function update(UpdateOrderRequest $request, Order $order): JsonResponse
    {
        $order->update(['status' => $request->status]);

        return $this->success(
            new OrderResource($order->fresh()->load(['items.product', 'user'])),
            'Order updated successfully'
        );
    }

    /**
     * Remove the specified order (Admin only)
     */
    public function destroy(Order $order): JsonResponse
    {
        $order->delete();

        return $this->noContent('Order deleted successfully');
    }
}
