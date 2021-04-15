<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RestaurantSubOrder extends Model
{
    protected $hidden = ['pivot'];

    public $timestamps = false;

    protected $fillable = [
        "restaurant_id", 'sub_order_id'
    ];
    protected $table = "restaurant_sub_order";
}
