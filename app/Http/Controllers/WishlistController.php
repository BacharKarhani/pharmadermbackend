<?php

namespace App\Http\Controllers;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        return $request->user()->wishlist()->with('product')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $wishlist = Wishlist::firstOrCreate([
            'user_id' => $request->user()->id,
            'product_id' => $request->product_id,
        ]);

        return response()->json($wishlist->load('product'), 201);
    }

    public function destroy(Request $request, $product_id)
    {
        $wishlist = Wishlist::where('user_id', $request->user()->id)
            ->where('product_id', $product_id)
            ->first();
    
        if (! $wishlist) {
            return response()->json(['message' => 'Product not found in wishlist'], 404);
        }
    
        $wishlist->delete();
    
        return response()->json(['message' => 'Product removed from wishlist successfully.']);
    }
    
}
