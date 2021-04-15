<?php

namespace App\Http\Controllers\ApiController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Notifications\BanNotification;
use App\Notifications\LiftBanNotification;

class BanController extends Controller
{
    public function index(Request $request)
    {
        $ban = \DB::table('ban')->select(['ban.id as ban_id', 'user.id as user_id', 'restaurant.id as restaurant_id', 'user.email', 'ban.created_at'])
                    ->join('user_ban', 'user_ban.ban_id', '=', 'ban.id')
                    ->join('user', 'user.id', '=', 'user_ban.user_id')
                    ->leftJoin('user_restaurant', 'user_restaurant.user_id', '=', 'user.id')
                    ->leftJoin('restaurant', 'restaurant.id', '=', 'user_restaurant.restaurant_id')
                    ->orderBy('ban.created_at', 'DESC')
                    ->get();
        
        return response()->json([
            "success" => true,
            "data" => $ban
        ]);
    }

    public function show($ban_id, Request $request)
    {
        $ban = $this->ban()->where('id', $ban_id)->get()->first();

        if (!$ban) {
            return response()->json([
                "success" => false,
                "message" => "Ban ID does not exist!"
            ]);
        }

        $user = $this->ban()->find($ban_id)->user()->get()->first();
        $customer = $this->user()->find($user['id'])->customer()->get()->first();
        $report = $this->customer()->find($customer['id'])->report()->get();

        return response()->json([
            "success" => true,
            "data" => [
                "ban" => $ban,
                "user" => $user,
                "customer" => $customer,
                "reports" => $report
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:user,id',
            'ban_reason' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => "Invalid Input",
                "errors" => $validator->errors()
            ]);
        }

        $user_id = $request->get('user_id');
        $ban = $this->user()->find($user_id)->ban()->get()->first();
        if ($ban) {
           return response()->json([
                "success" => false,
                "message" => "This user is already banned!"
           ]);
        }

        $this->user()->find($user_id)->ban()->create([
            "reason" => $request->get('ban_reason')
        ]);

        //$this->user()->find($user_id)->notify(new BanNotification());
        
        if ($request->header()['origin'][0] == "app") {
            $this->logs()->create([
                "user_id" => auth()->user()->id,
                "ip_address" => $request->ip(),
                "type" => "Insert",
                "description" => "Ban User ID : ".$user_id,
                "origin" => $request->header()['origin'][0]
            ]);
        }

        return response()->json([
            "success" => true,
            "message" => "Successfully banned the user!"
        ]);
    }

    public function destroy($user_id, Request $request)
    {
        $user = $this->user()->find($user_id)->get()->first();

        if (!$user) {
            return response()->json([
                "success" => false,
                "message" => "User does not exist!"
            ]);
        }

        $ban = $this->user()->find($user_id)->ban()->get()->first();

        if (!$ban) {
            return response()->json([
                "success" => false,
                "message" => "This user is not banned!"
            ]);
        }

        $this->user()->find($user_id)->ban()->detach($ban['id']);
        $this->ban()->find($ban['id'])->delete();

        //$this->user()->find($user_id)->notify(new LiftBanNotification());

        if ($request->header()['origin'][0] == "app") {
            $this->logs()->create([
                "user_id" => auth()->user()->id,
                "ip_address" => $request->ip(),
                "type" => "Delete",
                "description" => "Unban User ID : ".$user_id,
                "origin" => $request->header()['origin'][0]
            ]);
        }

        return response()->json([
            "success" => true,
            "message" => "Successfully lifted the ban!"
        ]);
    }
}
