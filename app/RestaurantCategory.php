<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RestaurantCategory extends Model
{
    protected $fillable = [
        "restaurant_id", 'category_id'
    ];



    protected $hidden = ['pivot'];

    public $timestamps = false;
}
