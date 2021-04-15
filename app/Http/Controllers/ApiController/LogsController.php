<?php

namespace App\Http\Controllers\ApiController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LogsController extends Controller
{
    public function table(Request $request)
    {
        $logs = \DB::table('logs')->select(['logs.ip_address', 'user.email', 'logs.type', 'logs.description', 'logs.origin', 'logs.created_at', 'customer.id as customer_id', 'restaurant.id as restaurant_id'])
                ->join('user', 'user.id', '=', 'logs.user_id')
                ->leftJoin('user_customer', 'user_customer.user_id', '=', 'user.id')
                ->leftJoin('customer', 'customer.id', '=', 'user_customer.customer_id')
                ->leftJoin('user_restaurant', 'user_restaurant.user_id', '=', 'user.id')
                ->leftJoin('restaurant', 'restaurant.id', '=', 'user_restaurant.restaurant_id')
                ->orderBy('logs.created_at', 'desc')
                ->get();

        return response()->json([
            "success" => true,
            "data" => $logs
        ]);        
    }

    public function index(Request $request)
    {
        $user_id = auth()->user()->id;
        $logs = $this->logs()->where('user_id', $user_id)->orderBy('logs.created_at', 'DESC');

        if ($request->has('filter')) {
            if ($request->get('filter') !== null) {
                $logs->where('origin', $request->get('filter'));
            }
        }

        if ($request->has('type')) {
            if ($request->get('type') !== null) {
                $logs->where('type', $request->get('type'));
            }
        }
        
        return response()->json([
            "success" => true,
            "data" => $logs->get()
        ]);
    }
    
    public function store(Request $request)
    {
        $this->logs()->create([
            "user_id" => auth()->user()->id,
            "ip_address" => $request->get('ip_address'),
            "type" => $request->get('type'),
            "description" => $request->get('description'),
            "origin" => $request->header()['origin'][0]
        ]);
    }

    public function store1(Request $request)
    {
        $store_values = [
            "ip_address" => $request->get('ip_address'),
            "type" => $request->get('type'),
            "description" => $request->get('description'),
            "origin" => $request->header()['origin'][0]
        ];

        if ($request->has('user_id')) {
            $store_values['user_id'] = $request->get('user_id');
        }

        $this->logs()->create($store_values);
    }    

}
