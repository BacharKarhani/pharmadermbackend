<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'address_id' => 'required|exists:addresses,id',
            'payment_code' => 'required|in:cash,wish', 
        ]);

        $user = $request->user();
        $cartItems = $user->cart()->with('product')->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Your cart is empty.'
            ], 400);
        }

        $subtotal = $cartItems->sum(fn($item) => $item->product->selling_price * $item->quantity);
        $shipping = 3;
        $total = $subtotal + $shipping;

        DB::beginTransaction();

        try {
            $order = Order::create([
                'user_id' => $user->id,
                'address_id' => $request->address_id,
                'subtotal' => $subtotal,
                'shipping' => $shipping,
                'total' => $total,
                'payment_code' => $request->payment_code,
                'order_status' => 'pending',
                'date_added' => now(),
            ]);

            foreach ($cartItems as $item) {
                // Create order product
                OrderProduct::create([
                    'order_id' => $order->order_id,
                    'product_id' => $item->product_id,
                    'price' => $item->product->selling_price,
                    'quantity' => $item->quantity,
                    'total' => $item->product->selling_price * $item->quantity,
                ]);

                // Reduce product quantity
                $item->product->decrement('quantity', $item->quantity);
            }

            // Clear the cart after successful order
            $user->cart()->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully.',
                'order' => $order->load('user', 'address'),
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
