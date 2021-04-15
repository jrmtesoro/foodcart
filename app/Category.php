<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class Category extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $table = "category";
    public $timestamps = true;
    
    protected $softDelete = true;

    protected $fillable = [
        'name'
    ];
    protected $hidden = ['pivot'];

    public function menu()
    {
        return $this->belongsToMany('App\Menu', 'menu_category');
    }

    public function restaurant()
    {
        return $this->belongsToMany('App\Restaurant', 'restaurant_category');
    }

    public function getCategory($get, $restaurant_id, $order_by)
    {
        $category = DB::table('category')->select($get)
                    ->join('restaurant_category', 'restaurant_category.category_id', '=', 'category.id')
                    ->join('restaurant', 'restaurant.id', '=', 'restaurant_category.restaurant_id')
                    ->whereNull('category.deleted_at')
                    ->where('restaurant.id', $restaurant_id)
                    ->orderBy($order_by[0], $order_by[1])
                    ->get();
        return $category;
    }

    public function deletedCategory($restaurant_id)
    {
        $category = DB::table('category')->select('category.id', 'category.name', 'category.deleted_at')
                    ->join('restaurant_category', 'restaurant_category.category_id', '=', 'category.id')
                    ->join('restaurant', 'restaurant.id', '=', 'restaurant_category.restaurant_id')
                    ->whereNotNull('category.deleted_at')
                    ->where('restaurant.id', $restaurant_id)
                    ->orderBy('category.deleted_at', 'DESC')
                    ->get();
        return $category;
    }

    public function publicCategory($restaurant_id)
    {
        $category = DB::table('category')->select('category.name')
                    ->join('restaurant_category', 'restaurant_category.category_id', '=', 'category.id')
                    ->join('restaurant', 'restaurant.id', '=', 'restaurant_category.restaurant_id')
                    ->whereNull('category.deleted_at')
                    ->where('restaurant.id', $restaurant_id)
                    ->orderBy('category.name', 'ASC')
                    ->get();
                    
        return $category;
    }
}
