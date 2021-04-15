<?php

namespace App\Http\Controllers\ApiController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminOrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = \DB::table('orders')->select(['orders.id', 'customer.fname', 'customer.lname', 'restaurant.name', 'orders.code', 'orders.status as order_status', 'orders.created_at'])
                ->join('order_sub_order', 'order_sub_order.order_id', '=', 'orders.id')
                ->join('sub_orders', 'sub_orders.id', '=', 'order_sub_order.sub_order_id')
                ->join('restaurant_sub_order', 'restaurant_sub_order.sub_order_id', '=', 'sub_orders.id')
                ->join('restaurant', 'restaurant.id', '=', 'restaurant_sub_order.restaurant_id')
                ->join('customer_order', 'customer_order.order_id', '=', 'orders.id')
                ->join('customer', 'customer.id', '=', 'customer_order.customer_id')
                ->orderBy('orders.created_at', 'DESC')
                ->groupBy('orders.id');

        if ($request->has('status')) {
            if ($request->get('status') !== null) {
                $status = $request->get('status');
                if ($status == "processing") {
                    $orders->whereIn('orders.status', [0,1,2]);
                } else if ($status == "completed") {
                    $orders->whereIn('orders.status', [3,4,5]);
                }
            }
        }
        
        return response()->json([
            "success" => true,
            "data" => $orders->get()
        ]);
    }

    public function show($order_id, Request $request)
    {
        $order = $this->order()->where('id', $order_id)->exists();
        
        if (!$order) {
            return response()->json([
                "success" => false,
                "message" => "No order found."
            ]);
        }

        $order = $this->order()->where('id', $order_id)->with(['suborder' => function ($query) {
            $query->with(['itemlist']);
        }])->get()->first();
        $order['date'] = $order->created_at->format('F d, Y h:i A');

        $indx = 0;
        foreach ($order['suborder'] as $suborder) {
            $rest = $this->suborder()->find($suborder['id'])->restaurant()->select(['id', 'slug', 'name', 'flat_rate', 'eta', 'contact_number'])->get()->first();
            $order['suborder'][$indx]['restaurant'] = $rest;
            $indx++;
        }

        return response()->json([
            "success" => true,
            "data" => $order
        ]);
    }
}
