<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserRestaurant extends Model
{
    protected $fillable = [
        "user_id", "restaurant_id"
    ];

    protected $table = "user_restaurant";
    public $timestamps = false;
}
