<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = "orders";
    protected $hidden = ['pivot'];

    protected $fillable = [
        'total', 'cooking_time_total', 'status', 'code'
    ];

    public $timestamps = true;

    public function customer()
    {
        return $this->belongsToMany('App\Customer', 'customer_order');
    }

    public function suborder()
    {
        return $this->belongsToMany('App\SubOrder', 'order_sub_order');
    }

    public function report()
    {
        return $this->belongsToMany('App\Report', 'order_report');
    }

    public static function getOldest()
    {
        return \App\Order::oldest()->value('created_at')->format('m-d-Y');
    }

    public function updateStatus($sub_order_id)
    {
        $order = \DB::table('orders')->select(['orders.status', 'orders.code'])
                ->join('order_sub_order', 'order_sub_order.order_id', '=', 'orders.id')
                ->join('sub_orders', 'sub_orders.id', '=', 'order_sub_order.sub_order_id')
                ->where('sub_orders.id', $sub_order_id)
                ->get()->first();
        
        $sub_orders = \App\SubOrder::where('id', $sub_order_id)->get();

        $status_array = [];
        foreach ($sub_orders as $sub_order) {
            $status = $sub_order->status;
            $status_array[] = $status;
        }
        
        
        $filtered = array_filter($status_array);
        $updated_status = 0;
        if (empty($filtered)) {
            $updated_status = 0;
        } else if (min($filtered) == 1) {
            $updated_status = 1;
        } else if (min($filtered) == 2) { 
            $updated_status = 2;
        } else if (min($filtered) == 3) {
            $updated_status = 3;
        } else if (min($status_array) == 0) {
            $updated_status = 0;
        } else if (min($status_array) == 4) {
            $updated_status = 4;
        } else if (min($status_array) == 5) {;
            $updated_status = 5;
        }

        return $updated_status;
    }

    public function orderCheck($sub_order_id, $restaurant_id)
    {
        $orders = \DB::table('sub_orders')
                ->selectRaw('*')
                ->join('sub_order_item_list', 'sub_order_item_list.sub_order_id', '=', 'sub_orders.id')
                ->join('restaurant_sub_order', 'restaurant_sub_order.sub_order_id', '=', 'sub_orders.id')
                ->join('restaurant', 'restaurant.id', '=', 'restaurant_sub_order.restaurant_id')
                ->where('restaurant.id', $restaurant_id)
                ->where('sub_orders.id', $sub_order_id)
                ->groupBy('sub_orders.id')
                ->get()->first();

        return $orders;
    }
}
