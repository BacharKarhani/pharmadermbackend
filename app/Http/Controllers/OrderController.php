<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // Get all orders paginated with optional filters
    public function indexPaginated(Request $request)
    {
        $query = Order::with('user', 'address');

        // Filter by first and last name
        if ($request->filled('fname') && $request->filled('lname')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('fname', 'like', '%' . $request->fname . '%')
                  ->where('lname', 'like', '%' . $request->lname . '%');
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('order_status', $request->status);
        }

        // Filter by payment method
        if ($request->filled('payment_code')) {
            $query->where('payment_code', $request->payment_code);
        }

        // Filter by date range
        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('date_added', [$request->from, $request->to]);
        }

        $orders = $query->orderByDesc('date_added')->paginate(10);

        if ($orders->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ]);
        }

        return response()->json([
            'success' => true,
            'orders' => $orders
        ]);
    }

    // Get single order by ID
    public function show($order_id)
    {
        $order = Order::with('user', 'address')->find($order_id);

        if (! $order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'order' => $order
        ]);
    }
}
