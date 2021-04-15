<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RestaurantPermit extends Model
{
    protected $hidden = ['pivot'];

    public $timestamps = false;

    protected $fillable = [
        "restaurant_id", 'permit_id'
    ];
    protected $table = "restaurant_permit";
}
