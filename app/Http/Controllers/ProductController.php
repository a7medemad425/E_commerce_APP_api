<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // 🟢 عرض كل المنتجات
    public function index()
    {
        return response()->json(Product::all(), 200);
    }

    // 🟡 عرض منتج واحد
    public function show($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        return response()->json($product, 200);
    }

    // 🟣 إنشاء منتج جديد
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'image' => $imagePath,
        ]);

        return response()->json([
            'message' => 'Product created successfully',
            'data' => $product,
        ], 201);
    }

    // 🔵 تحديث منتج
    public function update(Request $request, $id)
{
    $product = Product::find($id);

    if (!$product) {
        return response()->json(['message' => 'Product not found'], 404);
    }

    $request->validate([
        'name' => 'sometimes|string|max:255',
        'description' => 'sometimes|string|nullable',
        'price' => 'sometimes|numeric',
        'quantity' => 'sometimes|integer',
        'image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
    ]);

    // تحديث البيانات النصية
    if ($request->has('name')) $product->name = $request->name;
    if ($request->has('description')) $product->description = $request->description;
    if ($request->has('price')) $product->price = $request->price;
    if ($request->has('quantity')) $product->quantity = $request->quantity;

    // تحديث الصورة لو تم رفعها
    if ($request->hasFile('image')) {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        $product->image = $request->file('image')->store('products', 'public');
    }

    $product->save();

    return response()->json([
        'message' => 'Product updated successfully',
        'data' => $product,
    ]);
}



    // 🔴 حذف منتج
    public function destroy($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }
}
