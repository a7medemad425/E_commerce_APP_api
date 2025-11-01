<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // عرض كل الطلبات للمستخدم الحالي
    public function index(Request $request)
    {
        $user = $request->user();

        $orders = Order::with('items.product')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'message' => 'Orders fetched successfully',
            'orders' => $orders
        ]);
    }

    // عرض تفاصيل طلب معيّن
    public function show($id, Request $request)
    {
        $user = $request->user();

        $order = Order::with('items.product')
            ->where('user_id', $user->id)
            ->findOrFail($id);

        return response()->json([
            'message' => 'Order details fetched successfully',
            'order' => $order
        ]);
    }

   public function updateStatus(Request $request, $id)
{
    $user = $request->user();

    // السماح فقط للأدمن
    if (!$user->is_admin) {
        return response()->json([
            'message' => 'Unauthorized – Only admins can update order status'
        ], 403);
    }

    $request->validate([
        'status' => 'required|in:pending,paid,shipped,delivered,cancelled'
    ]);

    $order = Order::findOrFail($id);
    $order->status = $request->status;
    $order->save();

    return response()->json([
        'message' => 'Order status updated successfully',
        'order' => $order
    ]);
}


}

