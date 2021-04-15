<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class RestaurantMenu extends Model
{

    protected $fillable = [
        "restaurant_id", "menu_id"
    ];

    public $timestamps = false;
    
    protected $table = "restaurant_menu";

    public function Restaurant() 
    {
        return $this->belongsTo('App\Restaurant', 'restaurant_id');
    }

    public function Menu() 
    {
        return $this->belongsTo('App\Menu', 'menu_id');
    }
}
