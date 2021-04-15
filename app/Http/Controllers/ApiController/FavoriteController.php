<?php

namespace App\Http\Controllers\ApiController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $customer_id = auth()->user()->customer()->value('id');

        $favorite_list = $this->favorite()->customerFavorite($customer_id);

        return response()->json([
            "success" => true,
            "data" => $favorite_list
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "restaurant_slug" => "required|exists:restaurant,slug"
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => "Something went wrong.",
                "errors" => $validator->errors()
            ]);
        }

        $customer_id = auth()->user()->customer()->value('id');

        $customer_favorite = $this->favorite()->favoriteCheck($customer_id, $request->get('restaurant_slug'));

        if ($customer_favorite) {
            return response()->json([
                "success" => false,
                "message" => "This restaurant is already in your favorite list!"
            ]);
        }

        $restaurant = $this->restaurant()->where('slug', $request->get('restaurant_slug'))->get()->first();

        $this->favorite()->insertFavorite($customer_id, $restaurant['id']);

        if ($request->header()['origin'][0] == "app") {
            $this->logs()->create([
                "user_id" => auth()->user()->id,
                "ip_address" => $request->ip(),
                "type" => "Insert",
                "description" => "Added Restaurant Slug : ".$request->get('restaurant_slug')." to favorite",
                "origin" => $request->header()['origin'][0]
            ]);
        }

        return response()->json([
            "success" => true,
            "message" => "Restaurant added to your favorite list!"
        ]);
    }

    public function destroy($restaurant_slug, Request $request)
    {
        $validator = Validator::make(["restaurant_slug" => $restaurant_slug], [
            "restaurant_slug" => "required|exists:restaurant,slug"
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => "Something went wrong.",
                "errors" => $validator->errors()
            ]);
        }

        $customer_id = auth()->user()->customer()->value('id');

        $customer_favorite = $this->favorite()->favoriteCheck($customer_id, $request->get('restaurant_slug'));

        if ($customer_favorite) {
            return response()->json([
                "success" => false,
                "message" => "This restaurant is not in your favorite list!"
            ]);
        }

        $restaurant = $this->restaurant()->where('slug', $restaurant_slug)->get()->first();

        $this->favorite()->deleteFavorite($customer_id, $restaurant['id']);

        if ($request->header()['origin'][0] == "app") {
            $this->logs()->create([
                "user_id" => auth()->user()->id,
                "ip_address" => $request->ip(),
                "type" => "Delete",
                "description" => "Deleted Restaurant Slug : ".$request->get('restaurant_slug')." to favorite",
                "origin" => $request->header()['origin'][0]
            ]);
        }

        return response()->json([
            "success" => true,
            "message" => "Successfully removed restaurant from your favorite list!"
        ]);
    }
}
