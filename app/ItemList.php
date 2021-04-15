<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ItemList extends Model
{
    protected $table = "item_list";
    protected $hidden = ['pivot'];

    protected $fillable = [
        'name', 'price', 'cooking_time', 'quantity', 'identifier'
    ];

    public $timestamps = false;
    
    public function menu()
    {
        return $this->belongsToMany('App\Menu', 'menu_item_list');
    }

    public function suborder()
    {
        return $this->belongsToMany('App\ItemList', 'sub_order_item_list');
    }

    public function restaurant()
    {
        return $this->belongsToMany('App\Restaurant', 'restaurant_item_list');
    }

    public function getIdentifier($menu_id, $restaurant_id)
    {
        return \DB::table('item_list')->select('*')
                ->join('restaurant_item_list', 'restaurant_item_list.item_list_id', '=', 'item_list.id')
                ->join('restaurant', 'restaurant.id', '=', 'restaurant_item_list.restaurant_id')
                ->join('menu_item_list', 'menu_item_list.item_list_id', '=', 'item_list.id')
                ->join('menu', 'menu.id', '=', 'menu_item_list.menu_id')
                ->where('menu.id', $menu_id)
                ->where('restaurant.id', $restaurant_id)
                ->get();
    }
}
