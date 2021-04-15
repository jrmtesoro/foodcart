<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Cart extends Model
{
    protected $table = "cart";
    protected $hidden = ['pivot'];

    protected $fillable = [
        'quantity'
    ];

    public $timestamps = true;

    public function customer()
    {
        return $this->belongsToMany('App\Customer', 'customer_cart');
    }

    public function menu()
    {
        return $this->belongsToMany('App\Menu', 'menu_cart');
    }
    
    public function cartRestaurant($customer_id)
    {
        $cart = DB::table('customer')->select(['restaurant.id', 'restaurant.slug', 'restaurant.name', 'restaurant.flat_rate', 'restaurant.eta', 'restaurant.open_time', 'restaurant.close_time', 'restaurant.contact_number'])
                ->join('customer_cart', 'customer_cart.customer_id', '=', 'customer.id')
                ->join('cart', 'cart.id', '=', 'customer_cart.cart_id')
                ->join('menu_cart', 'menu_cart.cart_id', '=', 'cart.id')
                ->join('menu', 'menu.id', '=', 'menu_cart.menu_id')
                ->join('restaurant_menu', 'restaurant_menu.menu_id', '=', 'menu.id')
                ->join('restaurant', 'restaurant.id', '=', 'restaurant_menu.restaurant_id')
                ->where('customer.id', $customer_id)
                ->groupBy('restaurant.name')
                ->get();

        return $cart;
    }

    public function cartMenu($restaurant_slug, $customer_id)
    {
        $menu = DB::table('cart')->select(['menu.name', 'menu.price', 'menu.cooking_time', 'menu.slug', 'menu.image_name', 'cart.quantity', 'cart.id'])
                ->join('customer_cart', 'customer_cart.cart_id', '=', 'cart.id')
                ->join('customer', 'customer.id', '=', 'customer_cart.customer_id')
                ->join('menu_cart', 'menu_cart.cart_id', '=', 'cart.id')
                ->join('menu', 'menu.id', '=', 'menu_cart.menu_id')
                ->join('restaurant_menu', 'restaurant_menu.menu_id', '=', 'menu.id')
                ->join('restaurant', 'restaurant.id', '=', 'restaurant_menu.restaurant_id')
                ->where('restaurant.slug', $restaurant_slug)
                ->where('customer.id', $customer_id)
                ->get();
                
        return $menu;
    }

    public function cartCheckout($customer_id, $restaurant_id)
    {
        $menu = DB::table('customer')->select(['menu.id', 'menu.name', 'menu.price', 'menu.cooking_time', 'cart.quantity'])
                ->join('customer_cart', 'customer_cart.customer_id', '=', 'customer.id')
                ->join('cart', 'cart.id', '=', 'customer_cart.cart_id')
                ->join('menu_cart', 'menu_cart.cart_id', '=', 'cart.id')
                ->join('menu', 'menu.id', '=', 'menu_cart.menu_id')
                ->join('restaurant_menu', 'restaurant_menu.menu_id', '=', 'menu.id')
                ->join('restaurant', 'restaurant.id', '=', 'restaurant_menu.restaurant_id')
                ->where('restaurant.id', $restaurant_id)
                ->where('customer.id', $customer_id)
                ->get();
        return $menu;
    }

    public function cartCheck($customer_id, $menu_slug)
    {
        $menu = DB::table('customer')->select('cart.id')
                ->join('customer_cart', 'customer_cart.customer_id', '=', 'customer.id')
                ->join('cart', 'cart.id', '=', 'customer_cart.cart_id')
                ->join('menu_cart', 'menu_cart.cart_id', '=', 'cart.id')
                ->join('menu', 'menu.id', '=', 'menu_cart.menu_id')
                ->where('menu.slug', $menu_slug)
                ->where('customer.id', $customer_id)
                ->get()
                ->first();

        return $menu;
    }
}
