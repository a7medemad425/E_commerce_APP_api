<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;


class CheckoutController extends Controller
{
    
    
    public function checkout(Request $request)
{
    $user = $request->user();

    // نجيب المنتجات اللي في السلة
    $cartItems = $user->cartItems()->with('product')->get();

    if ($cartItems->isEmpty()) {
        return response()->json(['message' => 'Cart is empty'], 400);
    }

    // نحسب الإجمالي
    $total = 0;
    foreach ($cartItems as $item) {
        $total += $item->product->price * $item->quantity;
    }

    // نعمل الطلب
    $order = Order::create([
        'user_id' => $user->id,
        'total' => $total,
        'status' => 'pending'
    ]);

    // نضيف العناصر للـ order_items
    foreach ($cartItems as $item) {
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $item->product_id,
            'quantity' => $item->quantity,
            'price' => $item->product->price
        ]);
    }

    // نحذف السلة بعد الشراء
    $user->cartItems()->delete();

    return response()->json([
        'message' => 'Order created successfully',
        'order' => $order->load('items.product')
    ], 201);
}

}
