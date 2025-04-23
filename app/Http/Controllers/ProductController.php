<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // Public: list all products
    public function index()
    {
        return Product::with('category')->get();
    }

    // Public: show single product
    public function show(Product $product)
    {
        return $product->load('category');
    }

    // Admin: create product
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'desc' => 'nullable|string',
            'image' => 'nullable|image|max:2048', // optional image
            'category_id' => 'required|exists:categories,id',
        ]);

        // Only handle image if present
        $imagePath = $request->hasFile('image')
            ? $request->file('image')->store('products', 'public')
            : null;

        $product = Product::create([
            'name' => $request->name,
            'desc' => $request->desc,
            'image' => $imagePath,
            'category_id' => $request->category_id,
        ]);

        return response()->json($product, 201);
    }

    // Admin: update product
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string',
            'desc' => 'nullable|string',
            'image' => 'nullable|image|max:2048', // optional image
            'category_id' => 'required|exists:categories,id',
        ]);

        // Update image if a new one is uploaded
        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $product->image = $request->file('image')->store('products', 'public');
        }

        // Update other fields
        $product->update([
            'name' => $request->name,
            'desc' => $request->desc,
            'category_id' => $request->category_id,
            'image' => $product->image, // keep existing if not updated
        ]);

        return response()->json($product);
    }

    // Admin: delete product
    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted']);
    }
}
