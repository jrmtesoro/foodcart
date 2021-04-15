<?php

namespace App\Http\Controllers\ApiController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Notifications\AcceptChangeRequest;
use App\Notifications\RejectChangeRequest;

class ChangeRequestController extends Controller
{
    public function index(Request $request)
    {
        $requestchange = \DB::table('change_request')->select(['change_request.*', 'user.id as user_id', 'customer.id as customer_id', 'restaurant.id as restaurant_id'])
                        ->join('user_change_request', 'user_change_request.change_request_id', '=', 'change_request.id')
                        ->join('user', 'user.id', '=', 'user_change_request.user_id')
                        ->leftJoin('user_customer', 'user_customer.user_id', '=', 'user.id')
                        ->leftJoin('customer', 'customer.id', '=', 'user_customer.customer_id')
                        ->leftJoin('user_restaurant', 'user_restaurant.user_id', '=', 'user.id')
                        ->leftJoin('restaurant', 'restaurant.id', '=', 'user_restaurant.restaurant_id')
                        ->orderBy('change_request.created_at', 'desc');
        
         if ($request->has('filter')) {
            if ($request->get('filter') !== null) {
                $filter = $request->get('filter');
                if ($filter == "open") {
                    $requestchange->whereIn('change_request.status', ['0']);
                } else if ($filter == "close") {
                    $requestchange->whereIn('change_request.status', ['1','2']);
                }
            }
        }

        return response()->json([
            "success" => true,
            "data" => $requestchange->get()
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "request_new_email" => 'required|unique:user,email',
            "request_reason" => "required"
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => "Invalid Input",
                "errors" => $validator->errors()
            ]);
        }

        $user_id = auth()->user()->id;
        $email = auth()->user()->email;

        $change_request = $this->user()->find($user_id)->change_request()->where('status', 0)->get();

        if ($change_request->count() != 0) {
            return response()->json([
                "success" => false,
                "message" => "Request already sent! Please wait for the administrator's response"
            ]);
        }

        $this->user()->find($user_id)->change_request()->create([
            "reason" => $request->get('request_reason'),
            "new_email" => $request->get('request_new_email'),
            "old_email" => $email,
        ]);

        if ($request->header()['origin'][0] == "app") {
            $this->logs()->create([
                "user_id" => auth()->user()->id,
                "ip_address" => $request->ip(),
                "type" => "Insert",
                "description" => "Request Change Email Address",
                "origin" => $request->header()['origin'][0]
            ]);
        }

        return response()->json([
            "success" => true,
            "message" => "Successfully sent request for email change."
        ]);
    }

    public function accept($request_change_id, Request $request)
    {
        $request_change = $this->request_change()->where('id', $request_change_id)->get()->first();
        if (!$request_change) {
            return response()->json([
                "success" => false,
                "message" => "Change email request not found!"
            ]);
        }

        $request_status = $request_change['status'];

        if ($request_status == 1) {
            return response()->json([
                "success" => false,
                "message" => "Request already accepted!"
            ]);
        } else if ($request_status == 2) {
            return response()->json([
                "success" => false,
                "message" => "Request already rejected!"
            ]);
        }

        $this->request_change()->find($request_change_id)->update([
            "status" => 1
        ]);

        $user = $this->request_change()->find($request_change_id)->user()->get()->first();
        $user_id = $user['id'];

        $this->user()->find($user_id)->update([
            "email" => $request_change['new_email']
        ]);

        //$this->user()->find($user_id)->notify(new AcceptChangeRequest());

        if ($request->header()['origin'][0] == "app") {
            $this->logs()->create([
                "user_id" => auth()->user()->id,
                "ip_address" => $request->ip(),
                "type" => "Update",
                "description" => "Change email request of User ID : ".$user_id." accepted",
                "origin" => $request->header()['origin'][0]
            ]);
        }

        return response()->json([
            "success" => true,
            "data" => [
                "user_id" => $user_id
            ],
            "message" => "Successfully accepted change email request!"
        ]);
    }

    public function reject($request_change_id, Request $request)
    {
        $request_change = $this->request_change()->where('id', $request_change_id)->get()->first();
        if (!$request_change) {
            return response()->json([
                "success" => false,
                "message" => "Change email request not found!"
            ]);
        }

        $request_status = $request_change['status'];

        if ($request_status == 1) {
            return response()->json([
                "success" => false,
                "message" => "Request already accepted!"
            ]);
        } else if ($request_status == 2) {
            return response()->json([
                "success" => false,
                "message" => "Request already rejected!"
            ]);
        }
        
        $this->request_change()->find($request_change_id)->update([
            "status" => 2
        ]);

        $user = $this->request_change()->find($request_change_id)->user()->get()->first();
        $user_id = $user['id'];

        //$this->user()->find($user_id)->notify(new RejectChangeRequest());

        if ($request->header()['origin'][0] == "app") {
            $this->logs()->create([
                "user_id" => auth()->user()->id,
                "ip_address" => $request->ip(),
                "type" => "Update",
                "description" => "Change email request of User ID : ".$user_id." rejected",
                "origin" => $request->header()['origin'][0]
            ]);
        }

        return response()->json([
            "success" => true,
            "data" => [
                "user_id" => $user_id
            ],
            "message" => "Successfully rejected change email request!"
        ]);
    }
}
