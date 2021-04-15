<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Restaurant;

class SubOrder extends Model
{
    protected $fillable = [
        'total', 'cooking_time_total', 'status', 'expires_at', "origin", "payment"
    ];

    protected $hidden = ['pivot'];
    
    public $timestamps = true;

    protected $table = "sub_orders";
    
    const CREATED_AT = null;

    public function restaurant()
    {
        return $this->belongsToMany('App\Restaurant', 'restaurant_sub_order');
    }

    public function itemlist()
    {
        return $this->belongsToMany('App\ItemList', 'sub_order_item_list');
    }

    public function order()
    {
        return $this->belongsToMany('App\Order', 'order_sub_order');
    }

    public function report()
    {
        return $this->belongsToMany('App\Report', 'sub_order_report');
    }

    public function subOrderCheck($sub_order_id, $restaurant_id)
    {
        $suborder = DB::table('sub_orders')
                    ->join('restaurant_sub_order', 'restaurant_sub_order.sub_order_id', '=', 'sub_orders.id')
                    ->join('restaurant', 'restaurant.id', '=', 'restaurant_sub_order.restaurant_id')
                    ->where('restaurant.id', $restaurant_id)
                    ->where('sub_orders.id', $sub_order_id)
                    ->get()
                    ->first();

        return $suborder;
    }

    public static function getRestaurantSubOrderDate($restaurant_id, $interval)
    {
        $suborder = Restaurant::find($restaurant_id)->suborder()->orderBy('updated_at', 'ASC')->get()->first();

        $format = "m-d-Y";
        if ($interval == "monthly") {
            $format = "m-Y";
        } else {
            $format = "Y";
        }

        return \Carbon\Carbon::parse($suborder['updated_at'])->format($format);
    }

    public function orderFind($sub_order_id)
    {
        $suborder = DB::table('sub_orders')
                    ->select(['orders.id', 'orders.code', 'orders.created_at'])
                    ->join('order_sub_order', 'order_sub_order.sub_order_id', '=', 'sub_orders.id')
                    ->join('orders', 'orders.id', '=', 'order_sub_order.order_id')
                    ->where('sub_orders.id', $sub_order_id)
                    ->get()
                    ->first();

        return $suborder;
    }
}
