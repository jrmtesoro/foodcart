<?php

namespace App\Http\Controllers\ApiController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $users = \DB::table('user')->select(['user.*', 'customer.id as customer_id', 'restaurant.id as restaurant_id'])
                ->leftJoin('user_customer', 'user_customer.user_id', '=', 'user.id')
                ->leftJoin('customer', 'customer.id', '=', 'user_customer.customer_id')
                ->leftJoin('user_restaurant', 'user_restaurant.user_id', '=', 'user.id')
                ->leftJoin('restaurant', 'restaurant.id', '=', 'user_restaurant.restaurant_id')
                ->get();

        return response()->json([
            "success" => true,
            "data" => $users
        ]);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'reg_fname' => 'required|min:3|max:30',
            'reg_lname' => 'required|min:3|max:30',
            'reg_contact_number' => 'required|min:7|max:11',
            'reg_address' => 'required',
            'reg_email' => 'required|unique:user,email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => "Invalid Input",
                "errors" => $validator->errors()
            ]);
        };

        $password = str_random(20);
        $user_details = array(
            'email' => $request->get('reg_email'),
            'password' => bcrypt($password),
            'access_level' => 3
        );

        $user = $this->user()->create($user_details);

        $admin_details = array(
            "fname" => $request->get('reg_fname'),
            "lname" => $request->get('reg_lname'),
            "contact_number" => $request->get('reg_contact_number'),
            "address" => $request->get('reg_address')
        );

        $user->admin()->create($admin_details);
        $user->markEmailAsVerified();

        return response()->json([
            "success" => true,
            "message" => "Successfully generated an admin account!",
            "user_id" => $user->id,
            "email" => $request->get('reg_email'),
            "password" => $password
        ]);
    }
}
