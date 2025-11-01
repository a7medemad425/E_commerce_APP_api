<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    // 🟢 عرض محتوى السلة
    public function index(Request $request)
    {
        $cartItems = Cart::with('product')
            ->where('user_id', $request->user()->id)
            ->get();

        return response()->json($cartItems);
    }

    // 🟣 إضافة منتج للسلة
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1'
        ]);

        $product = Product::find($request->product_id);

        if ($product->quantity < ($request->quantity ?? 1)) {
            return response()->json(['message' => 'Not enough stock'], 400);
        }

        // لو المنتج موجود في السلة بالفعل، زوّد الكمية
        $cartItem = Cart::where('user_id', $request->user()->id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($cartItem) {
            $cartItem->quantity += $request->quantity ?? 1;
            $cartItem->save();
        } else {
            $cartItem = Cart::create([
                'user_id' => $request->user()->id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity ?? 1,
            ]);
        }

        return response()->json([
            'message' => 'Product added to cart successfully',
            'data' => $cartItem->load('product')
        ], 201);
    }

    // 🟡 تحديث الكمية
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cartItem = Cart::where('user_id', $request->user()->id)->find($id);
        if (!$cartItem) {
            return response()->json(['message' => 'Item not found in cart'], 404);
        }

        $cartItem->update(['quantity' => $request->quantity]);

        return response()->json([
            'message' => 'Cart item updated successfully',
            'data' => $cartItem->load('product')
        ]);
    }

    // 🔴 حذف منتج من السلة
    public function destroy(Request $request, $id)
    {
        $cartItem = Cart::where('user_id', $request->user()->id)->find($id);

        if (!$cartItem) {
            return response()->json(['message' => 'Item not found in cart'], 404);
        }

        $cartItem->delete();

        return response()->json(['message' => 'Product removed from cart']);
    }
}
