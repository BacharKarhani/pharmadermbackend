<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // Public: list all products
    public function index(Request $request)
    {
        $products = Product::with(['category', 'images'])->get();

        if (!auth('sanctum')->user() || auth('sanctum')->user()->role_id !== 1) {
            $products->makeHidden('buying_price');
        }

        return response()->json([
            'success' => true,
            'products' => $products
        ]);
    }

    public function show(Request $request, Product $product)
    {
        $product->load(['category', 'images']);

        if (!auth('sanctum')->user() || auth('sanctum')->user()->role_id !== 1) {
            $product->makeHidden('buying_price');
        }

        return response()->json([
            'success' => true,
            'product' => $product
        ]);
    }

    // Admin: create product with multiple images
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'desc' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'buying_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'images.*' => 'nullable|image|max:2048',
            'quantity' => 'required|integer|min:0',
        ]);

        $product = Product::create([
            'name' => $request->name,
            'desc' => $request->desc,
            'category_id' => $request->category_id,
            'buying_price' => $request->buying_price,
            'selling_price' => $request->selling_price,
            'quantity' => $request->quantity,
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                $product->images()->create(['path' => $path]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'product' => $product->load('images')
        ], 201);
    }

    // Admin: update product and optionally add new images
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string',
            'desc' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'buying_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'images.*' => 'nullable|image|max:2048',
            'quantity' => 'required|integer|min:0',
        ]);

        $product->update([
            'name' => $request->name,
            'desc' => $request->desc,
            'category_id' => $request->category_id,
            'buying_price' => $request->buying_price,
            'selling_price' => $request->selling_price,
            'quantity' => $request->quantity,
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                $product->images()->create(['path' => $path]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'product' => $product->load('images')
        ]);
    }

    // Admin: delete product and all related images
    public function destroy(Product $product)
    {
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->path);
            $image->delete();
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully'
        ]);
    }
}
