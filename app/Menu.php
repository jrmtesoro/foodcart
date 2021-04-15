<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class Menu extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    
    protected $table = "menu";
    protected $fillable = [
        "name", "description", "price", "cooking_time", "image_name", "slug"
    ];
    
    protected $hidden = ['pivot'];

    public $timestamps = true;

    public function restaurant() 
    {
        return $this->belongsToMany('App\Restaurant', 'restaurant_menu');
    }

    public function tag()
    {
        return $this->belongsToMany('App\Tag', 'menu_tag');
    }

    public function category()
    {
        return $this->belongsToMany('App\Category', 'menu_category');
    }

    public function itemlist()
    {
        return $this->belongsToMany('App\ItemList', 'menu_item_list');
    }

    public function cart()
    {
        return $this->belongsToMany('App\Cart', 'menu_cart');
    }

    public function getRelatedSlugs($slug, $id = 0)
    {
        return Menu::select('slug')->where('slug', 'like', $slug.'%')
            ->where('id', '<>', $id)
            ->get();
    }
    
    public function getMenu($menu_id, $restaurant_id) {
        $menu = \App\Restaurant::find($restaurant_id)->menu()
                ->with(['tag' => function ($query) {
                    $query->select(['name', 'status']);
                }, 'category' => function ($query){
                    $query->select(['id','name']);
                }])
                ->where('id', $menu_id)
                ->get();
        return $menu;
    }

    public function getMenus($get, $restaurant_id, $search = null)
    {
        $menu = DB::table('menu')->select($get)
                    ->join('restaurant_menu', 'restaurant_menu.menu_id', '=', 'menu.id')
                    ->join('restaurant', 'restaurant.id', '=', 'restaurant_menu.restaurant_id')
                    ->join('menu_category', 'menu_category.menu_id', '=', 'menu.id')
                    ->join('category', 'category.id', '=', 'menu_category.category_id')
                    ->where('restaurant.id', $restaurant_id)
                    ->whereNull('menu.deleted_at');
        if (!empty($search)) {
            $search_query = '%'.$search.'%';
            $menu = $menu->whereRaw('(menu.name LIKE ? OR menu.description LIKE ?)', [$search_query, $search_query]);
        }
        return $menu->orderBy('menu.created_at', 'DESC')->get();
    }

    public function insertMenu($values)
    {
        $menu = Menu::create($values);
        return $menu;
    }
}
