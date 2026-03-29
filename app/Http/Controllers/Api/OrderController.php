<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Events\OrderPlaced;

class OrderController extends Controller
{
    // 📦 Get all user orders
    public function index(Request $request)
    {
        $orders = $request->user()
            ->orders()
            ->with('items.product')
            ->latest()
            ->get();

        return response()->json($orders);
    }

    // 🔥 Checkout (MOST IMPORTANT)
    public function checkout(Request $request)
    {
        $user = $request->user();

        $cartItems = $user->cartItems()->with('product')->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'message' => 'Cart is empty'
            ], 400);
        }

        $total = 0;

        // 🧠 Calculate total + check stock
        foreach ($cartItems as $item) {

            if ($item->quantity > $item->product->stock) {
                return response()->json([
                    'message' => 'Not enough stock for ' . $item->product->name
                ], 400);
            }

            $total += $item->quantity * $item->product->price;
        }

        // 🧾 Create order
        $order = Order::create([
            'user_id' => $user->id,
            'total_price' => $total,
            'status' => 'pending',
        ]);

        // 📦 Create order items
        foreach ($cartItems as $item) {

            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $item->product_id,
                'quantity'   => $item->quantity,
                'price'      => $item->product->price,
            ]);
        }

        // 🧹 Clear cart
        $user->cartItems()->delete();

        // 🚀 Dispatch Event (Queued Listeners will handle stock & email)
        event(new OrderPlaced($order));

        return response()->json([
            'message' => 'Order placed successfully',
            'order' => $order
        ], 201);
    }

    // 🔍 Show single order (optional but good)
    public function show(Request $request, $id)
    {
        $order = $request->user()
            ->orders()
            ->with('items.product')
            ->find($id);

        if (!$order) {
            return response()->json([
                'message' => 'Order not found'
            ], 404);
        }

        return response()->json($order);
    }
}
