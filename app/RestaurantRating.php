<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RestaurantRating extends Model
{
    protected $hidden = ['pivot'];

    public $timestamps = false;

    protected $fillable = [
        "restaurant_id", 'rating_id'
    ];
    protected $table = "restaurant_rating";
}
