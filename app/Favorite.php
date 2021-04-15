<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Favorite extends Model
{
    protected $table = "favorite";
    protected $hidden = ['pivot'];

    protected $fillable = [
        'restaurant_id', 'customer_id'
    ];

    public $timestamps = false;

    public function insertFavorite($customer_id, $restaurant_id)
    {
        return DB::table('favorite')->insert([
            "customer_id" => $customer_id,
            "restaurant_id" => $restaurant_id
        ]);
    }

    public function deleteFavorite($customer_id, $restaurant_id)
    {
        return DB::table('favorite')
            ->where('customer_id', $customer_id)
            ->where('restaurant_id', $restaurant_id)
            ->delete();
    }

    public function customerFavorite($customer_id)
    {
        return DB::table('customer')->select(['restaurant.id', 'restaurant.name', 'restaurant.slug', 'restaurant.address', 'restaurant.image_name', 'restaurant.rating'])
                ->join('favorite', 'favorite.customer_id', '=', 'customer.id')
                ->join('restaurant', 'restaurant.id', '=', 'favorite.restaurant_id')
                ->where('customer.id', $customer_id)
                ->get();
    }

    public function favoriteCheck($customer_id, $restaurant_slug)
    {
        return DB::table('customer')->select(['restaurant.id', 'restaurant.name', 'restaurant.slug'])
                ->join('favorite', 'favorite.customer_id', '=', 'customer.id')
                ->join('restaurant', 'restaurant.id', '=', 'favorite.restaurant_id')
                ->where('customer.id', $customer_id)
                ->where('restaurant.slug', $restaurant_slug)
                ->get()->first();
    }
}
