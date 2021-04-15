<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RestaurantItemList extends Model
{
    protected $hidden = ['pivot'];

    public $timestamps = false;

    protected $fillable = [
        "restaurant_id", 'item_list_id'
    ];
    protected $table = "restaurant_item_list";
}
