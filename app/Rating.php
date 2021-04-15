<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Rating extends Model
{
    protected $table = "rating";
    protected $hidden = ['pivot'];

    protected $fillable = [
        'rate'
    ];

    public $timestamps = false;

    public function restaurant()
    {
        return $this->belongsToMany('App\Restaurant', 'restaurant_rating');
    }

    public function customer()
    {
        return $this->belongsToMany('App\Restaurant', 'customer_rating');
    }

    public function restaurantRating($restaurant_id)
    {
        $restaurant_rating = DB::table('restaurant')->selectRaw('rating.rate, COUNT(*) as count')
                            ->join('restaurant_rating', 'restaurant_rating.restaurant_id', '=', 'restaurant.id')
                            ->join('rating', 'rating.id', '=', 'restaurant_rating.rating_id')
                            ->where('restaurant.id', $restaurant_id)
                            ->groupBy('rating.rate')
                            ->get();

        return $restaurant_rating;
    }

    public function customerRating($customer_id)
    {
        $customer_rating = DB::table('customer')->select(['restaurant.id', 'restaurant.slug', 'rating.rate'])
                            ->join('customer_rating', 'customer_rating.customer_id', '=', 'customer.id')
                            ->join('rating', 'rating.id', '=', 'customer_rating.rating_id')
                            ->join('restaurant_rating', 'restaurant_rating.rating_id', '=', 'rating.id')
                            ->join('restaurant', 'restaurant.id', '=', 'restaurant_rating.restaurant_id')
                            ->where('customer.id', $customer_id)
                            ->get();

        return $customer_rating;
    }

    public function customerRestaurantRating($customer_id, $restaurant_id)
    {
        return DB::table('customer')->select(['rating.id', 'rating.rate'])
                ->join('customer_rating', 'customer_rating.customer_id', '=', 'customer.id')
                ->join('rating', 'rating.id', '=', 'customer_rating.rating_id')
                ->join('restaurant_rating', 'restaurant_rating.rating_id', '=', 'rating.id')
                ->join('restaurant', 'restaurant.id', '=', 'restaurant_rating.restaurant_id')
                ->where('customer.id', $customer_id)
                ->where('restaurant.id', $restaurant_id)
                ->get()->first();
    }
}
