<?php

namespace App\Http\Controllers\ApiController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class RatingController extends Controller
{
    public function index()
    {
        $customer_id = auth()->user()->customer()->value('id');

        $customer_rating = $this->rating()->customerRating($customer_id);

        return response()->json([
            "success" => true,
            "data" => $customer_rating
        ]);
    }

    public function show($restaurant_slug)
    {

    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            "restaurant_slug" => "required|exists:restaurant,slug",
            "rating" => "required|numeric|between:0,5"
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => "Rate Restaurant Failed",
                "errors" => $validator->errors()
            ]);
        }

        $customer_id = auth()->user()->customer()->value('id');

        $restaurant = $this->restaurant()->where('slug', $request->get('restaurant_slug'))->get()->first();

        $customer_rating = $this->rating()->customerRestaurantRating($customer_id, $restaurant['id']);

        if ($customer_rating) {
            $this->rating()->find($customer_rating->id)->update([
                "rate" => $request->get('rating')
            ]);
        } else {
            $rating = $this->rating()->create(["rate" => $request->get('rating')]);

            $this->customer()->find($customer_id)->rating()->save($rating);
            $this->restaurant()->find($restaurant['id'])->rating()->save($rating);
        }

        $restaurant_ratings = $this->rating()->restaurantRating($restaurant['id']);

        $top = 0;
        $bot = 0;
        foreach ($restaurant_ratings as $rate) {
            $top += $rate->rate * $rate->count;
            $bot += $rate->count;
        }

        $total_rating = $top/$bot;

        $this->restaurant()->find($restaurant['id'])->update([
            "rating" => $total_rating
        ]);

        $votes = $this->restaurant()->find($restaurant['id'])->rating()->get();

        if (strlen($total_rating) > 3) {
            $total_rating = substr($total_rating, 0, 3);
        }

        $vote_count = 0;
        foreach ($votes as $vote) {
            $vote_count++;
        }

        $vote = "votes";
        if ($vote_count <= 1) {
            $vote = "vote";
        }
        
        if ($request->header()['origin'][0] == "app") {
            $this->logs()->create([
                "user_id" => auth()->user()->id,
                "ip_address" => $request->ip(),
                "type" => "Insert",
                "description" => "Rating ".$request->get('rating')." for Restaurant Slug : ".$request->get('restaurant_slug'),
                "origin" => $request->header()['origin'][0]
            ]);
        }

        return response()->json([
            "success" => true,
            "message" => "Thank you for rating the restaurant!",
            "data" => [
                "total_rating" => $total_rating,
                "vote" => "(".$vote_count." ".$vote.")"
            ]
        ]);
    }
}
