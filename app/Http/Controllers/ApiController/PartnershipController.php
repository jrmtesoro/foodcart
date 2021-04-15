<?php

namespace App\Http\Controllers\ApiController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notifications\RestaurantAccept;
use App\Notifications\RestaurantReject;

class PartnershipController extends Controller
{
    public function index(Request $request)
    {
        $restaurant = $this->restaurant()
                    ->selectRaw('restaurant.id, restaurant.name, restaurant.contact_number, restaurant.status, restaurant.created_at, user.email')
                    ->join('user_restaurant', 'user_restaurant.restaurant_id', '=', 'restaurant.id')
                    ->join('user', 'user.id', '=', 'user_restaurant.user_id');
        if ($request->has('filter')) {
            $filter = $request->get('filter');
            $status = 0;
            if ($filter == 'rejected') {
                $status = 2;
            } else if ($filter == 'accepted') {
                $status = 1;
            } else if ($filter == 'pending') {
                $status = 0;
            } else {
                return response()->json([
                    "success" => false,
                    "message" => "No Data Found."
                ]);
            }

            return response()->json([
                "success" => true,
                "data" => $restaurant->where('status', $status)->orderBy('restaurant.created_at', 'DESC')->get()
            ]);
        }

        return response()->json([
            "success" => true,
            "data" => $restaurant->get()
        ]);
    }
    
    public function show($restaurant, Request $request)
    {
        $restaurant_check = $this->restaurant()->find($restaurant)->user()->first();

        if (!$restaurant_check) {
            return response()->json([
                'success' => false,
                'message' => "Application doesn't exist!"
            ]);
        }
        
        $rest = $this->restaurant()->where('id', $restaurant)->with(['user', 'permit']);

        $restaurant_info = $rest->first()->toArray();
        $temp = $restaurant_info['user'][0];
        $restaurant_info['user'] = $temp;

        return response()->json([
            'success' => true,
            'data' => $restaurant_info
        ]);
    }

    public function accept($restaurant, Request $request)
    {
        $rest = $this->restaurant()->where('id', $restaurant);
        $rest_check = $rest->first();
        if (!$rest_check) {
            return response()->json([
                'success' => false,
                'message' => "Application doesn't exist!"
            ]);
        }

        $restaurant_array =  $rest->first()->toArray();
        $status = $restaurant_array['status'];

        if ($status == 1) {
            return response()->json([
                'success' => false,
                'message' => "The application is already accepted!"
            ]);
        } else if ($status == 2) {
            return response()->json([
                'success' => false,
                'message' => "The application is already rejected!"
            ]);
        }

        $rest->update([
            'status' => 1
        ]);
        
        $pass = str_random(10);
        
        $r = $this->restaurant()->find($restaurant)->user()->get()->first();
        $this->user()->find($r['id'])->markEmailAsVerified();

        $this->restaurant()->find($restaurant_array['id'])->user()->update(['password' => bcrypt($pass)]);

        $user = $this->restaurant()->find($restaurant_array['id'])->user();
        $user_array = $user->first()->toArray();
        //$this->user()->find($user_array['id'])->notify(new RestaurantAccept($user_array['email'], $pass));

        if ($request->header()['origin'][0] == "app") {
            $this->logs()->create([
                "user_id" => auth()->user()->id,
                "ip_address" => $request->ip(),
                "type" => "Update",
                "description" => "Restaurant ID : ".$restaurant." accepted",
                "origin" => $request->header()['origin'][0]
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => "Successfully accepted the restaurant application!",
            'data' => [
                'email' => $user_array['email'],
                'password' => $pass
            ]
        ]);
    }

    public function review($restaurant, Request $request) {
        $rest = $this->restaurant()->where('id', $restaurant);
        $rest_check = $rest->first();

        if (!$rest_check) {
            return response()->json([
                'success' => false,
                'message' => "Application doesn't exist!"
            ]);
        }

        $rest->update([
            'status' => 3
        ]);

        $restaurant_array = $rest_check->toArray();

        $user = $this->restaurant()->find($restaurant_array['id'])->user()->first()->toArray();

        if ($request->header()['origin'][0] == "app") {
            $this->logs()->create([
                "user_id" => auth()->user()->id,
                "ip_address" => $request->ip(),
                "type" => "Update",
                "description" => "Restaurant ID : ".$restaurant." review",
                "origin" => $request->header()['origin'][0]
            ]);
        }
        
        return response()->json([
            'success' => true,
            'message' => "Successfully updated the restaurant application status!"
        ]);
    }

    public function status(Request $request) {
        if (!$request->has('partner_email') || empty($request->get('partner_email')) || $request->get('partner_email') == "") {
            return response()->json([
                "success" => false,
                "message" => "Invalid Input"
            ]);
        }

        $partner_email = $request->get('partner_email');

        $user = $this->user()->where('email', $partner_email)->get()->first();

        if (!$user) {
            return response()->json([
                "success" => false,
                "message" => "No Email Address found!"
            ]);
        }

        $restaurant = $this->user()->find($user->id)->restaurant()->get()->first();

        if (!$restaurant) {
            return response()->json([
                "success" => false,
                "message" => "No Email Address found!"
            ]);
        }

        $restaurant_status = $restaurant->status;

        $msg = "pending";
        if ($restaurant_status == 1) {
            $msg = "accepted";
        } else if ($restaurant_status == 2) {
            $msg = "rejected";
        } else if ($restaurant_status == 3) {
            $msg = "reviewing";
        }

        return response()->json([
            "success" => true,
            "message" => $msg
        ]);
    }

    public function reject($restaurant, Request $request)
    {
        $rest = $this->restaurant()->where('id', $restaurant);
        $rest_check = $rest->first();

        if (!$rest_check) {
            return response()->json([
                'success' => false,
                'message' => "Application doesn't exist!"
            ]);
        }

        $restaurant_array = $rest_check->toArray();
        $status = $restaurant_array['status'];

        if ($status == 1) {
            return response()->json([
                'success' => false,
                'message' => "The application is already accepted!"
            ]);
        } else if ($status == 2) {
            return response()->json([
                'success' => false,
                'message' => "The application is already rejected!"
            ]);
        }

        $rest->update([
            'status' => 2
        ]);

        $user = $this->restaurant()->find($restaurant_array['id'])->user()->first()->toArray();
        
        //$this->user()->find($user['id'])->notify(new RestaurantReject());

        if ($request->header()['origin'][0] == "app") {
            $this->logs()->create([
                "user_id" => auth()->user()->id,
                "ip_address" => $request->ip(),
                "type" => "Update",
                "description" => "Restaurant ID : ".$restaurant." rejected",
                "origin" => $request->header()['origin'][0]
            ]);
        }
        
        return response()->json([
            'success' => true,
            'message' => "Successfully rejected the restaurant application!"
        ]);
    }
}
