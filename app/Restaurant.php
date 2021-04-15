<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Restaurant extends Model
{
    protected $fillable = [
        "owner_fname", "owner_lname",
        "name", "address", "contact_number", "slug", "status",
        "flat_rate", 'image_name', 'eta', 'open_time',
        'close_time', "rating"
    ];
    protected $hidden = ['pivot'];

    protected $table = "restaurant";

    public $timestamps = true;

    public function menu()
    {
        return $this->belongsToMany('App\Menu', 'restaurant_menu');
    }

    public function category()
    {
        return $this->belongsToMany('App\Category', 'restaurant_category');
    }

    public function user()
    {
        return $this->belongsToMany('App\User', 'user_restaurant');
    }

    public function permit()
    {
        return $this->belongsToMany('App\Permit', 'restaurant_permit');
    }

    public function suborder()
    {
        return $this->belongsToMany('App\SubOrder', 'restaurant_sub_order');
    }

    public function rating()
    {
        return $this->belongsToMany('App\Rating', 'restaurant_rating');
    }

    public function customer()
    {
        return $this->belongsToMany('App\Customer', 'favorite');
    }

    public function report()
    {
        return $this->belongsToMany('App\Report', 'restaurant_report');
    }

    public function itemlist()
    {
        return $this->belongsToMany('App\ItemList', 'restaurant_item_list');
    }

    public function restaurantTagSearch($tag_array, $restaurant_id_array)
    {
        $restaurants = \DB::table('restaurant')->select(['restaurant.*'])
                ->join('restaurant_menu', 'restaurant_menu.restaurant_id', '=', 'restaurant.id')
                ->join('menu', 'menu.id', '=', 'restaurant_menu.menu_id')
                ->join('menu_tag', 'menu_tag.menu_id', '=', 'menu.id')
                ->join('tag', 'tag.id', '=', 'menu_tag.tag_id')
                ->whereIn('tag.name', $tag_array)
                ->whereIn('restaurant.id', $restaurant_id_array)
                ->whereNotNull('flat_rate')
                ->whereNotNull('eta')
                ->whereNotNull('open_time')
                ->whereNotNull('close_time');

        return $restaurants->groupBy('restaurant.id')->get();
    }

    public function getRelatedSlugs($slug, $id = 0)
    {
        return Restaurant::select('slug')->where('slug', 'like', $slug.'%')
            ->where('id', '<>', $id)
            ->get();
    }

    public function totalOrders($restaurant_id, $year)
    {
        return \DB::table('orders')->select('orders.created_at')
                ->join('order_sub_order', 'order_sub_order.order_id', '=', 'orders.id')
                ->join('sub_orders', 'sub_orders.id', '=', 'order_sub_order.sub_order_id')
                ->join('restaurant_sub_order', 'restaurant_sub_order.sub_order_id', '=', 'sub_orders.id')
                ->join('restaurant', 'restaurant.id', '=', 'restaurant_sub_order.restaurant_id')
                ->where('sub_orders.status', 3)
                ->whereYear('orders.created_at', $year)
                ->where('restaurant.id', $restaurant_id)
                ->get()
                ->groupBy(function($d) {
                    return Carbon::parse($d->created_at)->format('M');
                });
    }

    public function totalSales($restaurant_id, $year)
    {
        return \DB::table('orders')->select(['sub_orders.total', 'orders.created_at'])
                ->join('order_sub_order', 'order_sub_order.order_id', '=', 'orders.id')
                ->join('sub_orders', 'sub_orders.id', '=', 'order_sub_order.sub_order_id')
                ->join('restaurant_sub_order', 'restaurant_sub_order.sub_order_id', '=', 'sub_orders.id')
                ->join('restaurant', 'restaurant.id', '=', 'restaurant_sub_order.restaurant_id')
                ->where('sub_orders.status', 3)
                ->whereYear('orders.created_at', $year)
                ->where('restaurant.id', $restaurant_id)
                ->get()
                ->groupBy(function($d) {
                    return Carbon::parse($d->created_at)->format('M');
                });
    }
}
