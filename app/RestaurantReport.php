<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RestaurantReport extends Model
{
    protected $hidden = ['pivot'];

    public $timestamps = false;

    protected $fillable = [
        "restaurant_id", 'report_id'
    ];
    protected $table = "restaurant_report";
}
