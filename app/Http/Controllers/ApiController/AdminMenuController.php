<?php

namespace App\Http\Controllers\ApiController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminMenuController extends Controller
{
    public function index(Request $request)
    {
        $menu = $this->menu()->get();

        $menu = \DB::table('menu')->select(['menu.id', 'menu.name', 'restaurant.name as resto_name', 'menu.price', 'menu.created_at'])
                ->join('restaurant_menu', 'restaurant_menu.menu_id', '=', 'menu.id')
                ->join('restaurant', 'restaurant.id', '=', 'restaurant_menu.restaurant_id')
                ->leftJoin('menu_tag', 'menu_tag.menu_id', '=', 'menu.id')
                ->leftJoin('tag', 'tag.id', '=', 'menu_tag.tag_id')
                ->orderBy('menu.created_at', 'DESC')
                ->groupBy('menu.id');

        if ($request->has('tag')) {
            if ($request->get('tag') !== null) {
                $menu->where('tag.slug', $request->get('tag'));
            }
        }

        if ($request->has('filter')) {
            if ($request->get('filter') !== null) {
                $filter = $request->get('filter');
                if ($filter == 'active') {
                    $menu->whereNull('menu.deleted_at');
                } else if ($filter == 'deleted') {
                    $menu->whereNotNull('menu.deleted_at');
                }
            }
        }

        return response()->json([
            "success" => true,
            "data" => $menu->get()
        ]);
    }

    public function show($menu_id, Request $request)
    {
        $menu = $this->menu()->where('id', $menu_id)->get()->first();

        if (!$menu) {
            return response()->json([
                "success" => false,
                "message" => "Menu does not exist!"
            ]);
        }

        $category = $this->menu()->find($menu_id)->category()->get()->first();
        $menu['category'] = $category['name'];

        $restaurant = $this->menu()->find($menu_id)->restaurant()->get()->first();
        $menu['restaurant_id'] = $restaurant['id'];
        $menu['restaurant_name'] = $restaurant['name'];

        $tag = $this->menu()->find($menu_id)->tag()->get();
        $menu['tag'] = $tag;

        return response()->json([
            "success" => true,
            "data" => $menu
        ]);
    }
}
