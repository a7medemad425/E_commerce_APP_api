<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function store(Request $request, $orderId)
    {
        $request->validate([
            'method' => 'required|in:cash_on_delivery,credit_card,paypal',
        ]);

        $user = $request->user();
        $order = Order::where('user_id', $user->id)->findOrFail($orderId);

        // لو الدفع عند الاستلام
        if ($request->method === 'cash_on_delivery') {
            $payment = Payment::create([
                'order_id' => $order->id,
                'method' => 'cash_on_delivery',
                'status' => 'pending',
            ]);

            $order->status = 'pending';
            $order->save();

            return response()->json([
                'message' => 'Order placed successfully (Cash on Delivery)',
                'payment' => $payment
            ]);
        }

        // لو دفع أونلاين (محاكاة بدون بوابة حقيقية)
        $transactionId = 'TXN-' . strtoupper(uniqid());

        $payment = Payment::create([
            'order_id' => $order->id,
            'method' => $request->method,
            'status' => 'paid',
            'transaction_id' => $transactionId
        ]);

        $order->status = 'paid';
        $order->save();

        return response()->json([
            'message' => 'Payment completed successfully',
            'payment' => $payment
        ]);
    }
}
