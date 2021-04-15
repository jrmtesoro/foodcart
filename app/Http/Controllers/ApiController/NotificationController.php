<?php

namespace App\Http\Controllers\ApiController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
    public function orders()
    {
        $restaurant_id = auth()->user()->restaurant()->value('id');
        $notification = \DB::table('sub_orders')->select(['orders.code', 'sub_orders.status', 'sub_orders.id'])
                        ->join('order_sub_order', 'order_sub_order.sub_order_id', '=', 'sub_orders.id')
                        ->join('orders', 'orders.id', '=', 'order_sub_order.order_id')
                        ->join('restaurant_sub_order', 'restaurant_sub_order.sub_order_id', '=', 'sub_orders.id')
                        ->join('restaurant', 'restaurant.id', '=', 'restaurant_sub_order.restaurant_id')
                        ->where('restaurant.id', $restaurant_id)
                        ->whereIn('sub_orders.status',[0,1,2])
                        ->orderBy('sub_orders.updated_at', 'DESC')
                        ->get();
        
        return response()->json([
            "success" => true,
            "data" => $notification->count(),
            "suborders" => $notification
        ]);
    }

    public function cart()
    {
        $customer_id = auth()->user()->customer()->value('id');

        $notification = $this->customer()->find($customer_id)->cart()->get();

        return response()->json([
            "success" => true,
            "data" => $notification->count()
        ]);
    }

    public function reports()
    {
        $reports = $this->report()->whereIn('status', [0,1])->get();

        return response()->json([
            "success" => true,
            "data" => $reports->count()
        ]);
    }

    public function tags()
    {
        $tags = $this->tag()->where('status', 0)->get();

        return response()->json([
            "success" => true,
            "data" => $tags->count()
        ]);
    }

    public function partnership()
    {
        $partnership = $this->restaurant()->where('status', 0)->get();

        return response()->json([
            "success" => true,
            "data" => $partnership->count()
        ]);
    }

    public function requests()
    {
        $request_ch = $this->request_change()->where('status', 0)->get();

        return response()->json([
            "success" => true,
            "data" => $request_ch->count()
        ]);
    }


    public function admin()
    {
        $reports = $this->report()->whereIn('status', [0,1])->get();
        $tags = $this->tag()->where('status', 0)->get();
        $partnership = $this->restaurant()->where('status', 0)->get();
        $change_req = $this->request_change()->where('status', 0)->get();

        return response()->json([
            "success" => true,
            "data" => array(
                "reports" => $reports->count(),
                "tags" => $tags->count(),
                "partnership" => $partnership->count(),
                "request" => $change_req->count()
            )
        ]);
    }
}
