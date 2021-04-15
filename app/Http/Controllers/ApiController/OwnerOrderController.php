<?php

namespace App\Http\Controllers\ApiController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Notifications\AcceptOrder;
use App\Notifications\DeliverOrder;
use App\Notifications\CompleteOrder;
use App\Notifications\RejectOrder;
use App\Notifications\CancelOrder;

class OwnerOrderController extends Controller
{
    public function index(Request $request)
    {
        $restaurant_id = auth()->user()->restaurant()->value('id');

        $sub_order = \DB::table('orders')
                ->selectRaw('sub_orders.id, orders.code, sub_orders.status, orders.created_at')
                ->join('order_sub_order', 'order_sub_order.order_id', '=', 'orders.id')
                ->join('sub_orders', 'sub_orders.id', '=', 'order_sub_order.sub_order_id')
                ->join('restaurant_sub_order', 'restaurant_sub_order.sub_order_id', '=', 'sub_orders.id')
                ->join('restaurant', 'restaurant.id', '=', 'restaurant_sub_order.restaurant_id')
                ->where('restaurant.id', $restaurant_id)
                ->groupBy('sub_orders.id')
                ->orderBy('orders.created_at', 'DESC');

        if ($request->has('status')) {
            if ($request->get('status') !== null) {
                $status = $request->get('status');
                
                if ($status == "processing") {
                    $sub_order->whereIn('sub_orders.status', [0,1,2]);
                } else if ($status == "completed") {
                    $sub_order->whereIn('sub_orders.status', [3,4,5]);
                }
            }
        }

        return response()->json([
            "success" => true,
            "data" => $sub_order->get()
        ]);
    }

    public function check()
    {
        $restaurant_id = auth()->user()->restaurant()->value('id');

        $sub_orders = $this->restaurant()->find($restaurant_id)->suborder()
            ->where('status', 3)
            ->get()->first();

        if (!$sub_orders) {
            return response()->json([
                "success" => false
            ]);
        }

        return response()->json([
            "success" => true
        ]);
    }
    
    public function show($suborder, Request $request)
    {
        $restaurant_id = auth()->user()->restaurant()->value('id');
        $flat_rate = auth()->user()->restaurant()->value('flat_rate');

        $order_check = $this->order()->orderCheck($suborder, $restaurant_id);

        if (!$order_check) {
            return response()->json([
                "success" => false,
                "message" => "Order doesn't exist!"
            ]);
        }

        $orders = $this->suborder()->where('id', $suborder)->with(['itemlist'])->get()->first();
        
        $order = $this->suborder()->orderFind($suborder);
        $customer = $this->order()->find($order->id)->customer()->get()->first();
        $user = $this->customer()->find($customer['id'])->user()->get()->first();
        $ban = $this->user()->find($user['id'])->ban()->exists();
        $reports = $this->customer()->find($customer['id'])->report()->get()->count();

        $orders['date_created'] = Carbon::parse($order->created_at)->format('F d, Y h:i A');
        $orders['date_expire'] = Carbon::parse($orders['expires_at'])->format('F d, Y h:i A');
        $orders['order'] = $order;
        $orders['customer'] = $customer;
        $orders['customer']['reports'] = $reports ?? 0;
        $orders['customer']['banned'] = $ban;
        $orders['flat_rate'] = intVal($flat_rate);
                
        return response()->json([
            "success" => true,
            "data" => $orders
        ]);
    }

    public function accept($suborder, Request $request)
    {
        $restaurant_id = auth()->user()->restaurant()->value('id');
        $sub_order_check = $this->suborder()->subOrderCheck($suborder, $restaurant_id);

        if (!$sub_order_check) {
            return response()->json([
                "success" => false, 
                "message" => "Order doesn't exist!"
            ]);
        }

        $orders = $this->suborder()->where('id', $suborder)->get()->first();

        if ($orders['status'] != "0") {
            return response()->json([
                "success" => false,
                "message" => "Order already accepted!"
            ]);
        }

        $expires_at = Carbon::now()->addMinutes($orders['cooking_time_total']);

        $accept = $this->suborder()->find($suborder)->update([
            "status" => 1,
            "expires_at" => $expires_at
        ]);

        $status = $this->order()->updateStatus($suborder);

        $this->suborder()->find($suborder)->order()->update([
            "status" => $status
        ]);

        $sub_order = $this->suborder()->find($suborder)->order()->get()->first();

        $order = $this->suborder()->find($suborder)->order()->first();
        $customer = $this->order()->find($order['id'])->customer()->first();
        $user = $this->customer()->find($customer['id'])->user()->first();
        $sub_order = $this->suborder()->find($suborder)->get()->first();
        $restaurant_name = auth()->user()->restaurant()->value('name');
        //$this->user()->find($user['id'])->notify(new AcceptOrder($order['code'], $restaurant_name, $sub_order['cooking_time_total']));

        if ($request->header()['origin'][0] == "app") {
            $this->logs()->create([
                "user_id" => auth()->user()->id,
                "ip_address" => $request->ip(),
                "type" => "Update",
                "description" => "Order #".$sub_order['code']." accepted",
                "origin" => $request->header()['origin'][0]
            ]);
        }

        return response()->json([
            "success" => true,
            "data" => [
                "order_code" => $sub_order['code']
            ],
            "message" => "Successfully accepted order!"
        ]);
    }

    public function reject($suborder, Request $request)
    {
        $restaurant_id = auth()->user()->restaurant()->value('id');
        $sub_order_check = $this->suborder()->subOrderCheck($suborder, $restaurant_id);

        if (!$sub_order_check) {
            return response()->json([
                "success" => false,
                "message" => "Order doesn't exist!"
            ]);
        }

        $orders = $this->suborder()->where('id', $suborder)->get()->first();

        if ($orders['status'] != 0) {
            if ($orders['status'] == 4) {
                return response()->json([
                    "success" => false,
                    "message" => "Order already rejected!"
                ]);
            } else {
                return response()->json([
                    "success" => false,
                    "message" => "Order already accepted!"
                ]);
            }
        }

        $accept = $this->suborder()->find($suborder)->update([
            "status" => 4
        ]);

        $status = $this->order()->updateStatus($suborder);

        $this->suborder()->find($suborder)->order()->update([
            "status" => $status
        ]);

        $sub_order = $this->suborder()->find($suborder)->order()->get()->first();

        $order = $this->suborder()->find($suborder)->order()->first();
        $customer = $this->order()->find($order['id'])->customer()->first();
        $user = $this->customer()->find($customer['id'])->user()->first();
        $sub_order = $this->suborder()->find($suborder)->get()->first();
        $restaurant_name = auth()->user()->restaurant()->value('name');
        //$this->user()->find($user['id'])->notify(new RejectOrder($order['code'], $restaurant_name));

        if ($request->header()['origin'][0] == "app") {
            $this->logs()->create([
                "user_id" => auth()->user()->id,
                "ip_address" => $request->ip(),
                "type" => "Update",
                "description" => "Order #".$sub_order['code']." rejected",
                "origin" => $request->header()['origin'][0]
            ]);
        }

        return response()->json([
            "success" => true,
            "data" => [
                "order_code" => $sub_order['code']
            ],
            "message" => "Successfully rejected order!"
        ]);
    }

    public function cancel($suborder, Request $request)
    {
        $restaurant_id = auth()->user()->restaurant()->value('id');
        $sub_order_check = $this->suborder()->subOrderCheck($suborder, $restaurant_id);

        if (!$sub_order_check) {
            return response()->json([
                "success" => false,
                "message" => "Order doesn't exist!"
            ]);
        }

        $orders = $this->suborder()->where('id', $suborder)->get()->first();

        $cancel = $this->suborder()->find($suborder)->update([
            "status" => 5
        ]);

        $status = $this->order()->updateStatus($suborder);

        $this->suborder()->find($suborder)->order()->update([
            "status" => $status
        ]);

        $sub_order = $this->suborder()->find($suborder)->order()->get()->first();

        $order = $this->suborder()->find($suborder)->order()->first();
        $customer = $this->order()->find($order['id'])->customer()->first();
        $user = $this->customer()->find($customer['id'])->user()->first();
        $sub_order = $this->suborder()->find($suborder)->get()->first();
        $restaurant_name = auth()->user()->restaurant()->value('name');
        //$this->user()->find($user['id'])->notify(new CancelOrder($order['code'], $restaurant_name));

        if ($request->header()['origin'][0] == "app") {
            $this->logs()->create([
                "user_id" => auth()->user()->id,
                "ip_address" => $request->ip(),
                "type" => "Update",
                "description" => "Order #".$sub_order['code']." cancelled",
                "origin" => $request->header()['origin'][0]
            ]);
        }

        return response()->json([
            "success" => true,
            "data" => [
                "order_code" => $sub_order['code']
            ],
            "message" => "Successfully cancelled order!"
        ]);

    }

    public function deliver($suborder, Request $request)
    {
        $restaurant_id = auth()->user()->restaurant()->value('id');
        $sub_order_check = $this->suborder()->subOrderCheck($suborder, $restaurant_id);

        if (!$sub_order_check) {
            return response()->json([
                "success" => false,
                "message" => "Order doesn't exist!"
            ]);
        }

        $orders = $this->suborder()->where('id', $suborder)->get()->first();

        if ($orders['status'] != 1) {
            if ($orders['status'] == 4) {
                return response()->json([
                    "success" => false,
                    "message" => "Order already rejected!"
                ]);
            } else if ($orders['status'] == 3) {
                return response()->json([
                    "success" => false,
                    "message" => "Order already completed!"
                ]);
            } else {
                return response()->json([
                    "success" => false,
                    "message" => "Order already accepted!"
                ]);
            }
        }

        $accept = $this->suborder()->find($suborder)->update([
            "status" => 2
        ]);

        $status = $this->order()->updateStatus($suborder);

        $this->suborder()->find($suborder)->order()->update([
            "status" => $status
        ]);

        $sub_order = $this->suborder()->find($suborder)->order()->get()->first();

        $order = $this->suborder()->find($suborder)->order()->first();
        $customer = $this->order()->find($order['id'])->customer()->first();
        $user = $this->customer()->find($customer['id'])->user()->first();
        $sub_order = $this->suborder()->find($suborder)->get()->first();
        $restaurant_name = auth()->user()->restaurant()->value('name');
        //$this->user()->find($user['id'])->notify(new DeliverOrder($order['code'], $restaurant_name));

        if ($request->header()['origin'][0] == "app") {
            $this->logs()->create([
                "user_id" => auth()->user()->id,
                "ip_address" => $request->ip(),
                "type" => "Update",
                "description" => "Order #".$sub_order['code']." delivered",
                "origin" => $request->header()['origin'][0]
            ]);
        }

        return response()->json([
            "success" => true,
            "data" => [
                "order_code" => $sub_order['code']
            ],
            "message" => "Successfully updated order status!"
        ]);
    }
    
    public function complete1($suborder, Request $request)
    {
        $sub_order = $this->suborder()->where('id', $suborder)->get()->first();
        
        $sub_order_status = $sub_order['status'];

        if ($sub_order_status != 2) {
            return response()->json([
                "success" => false,
                "message" => "You cannot complete this order!"
            ]);
        }
        
        $accept = $this->suborder()->find($suborder)->update([
            "status" => 3
        ]);
        
        $status = $this->order()->updateStatus($suborder);

        $this->suborder()->find($suborder)->order()->update([
            "status" => $status
        ]);
        
        return response()->json([
            "success" => true,
            "message" => "Successfully completed order!"
        ]);
    }

    public function complete($suborder, Request $request)
    {
        $restaurant_id = auth()->user()->restaurant()->value('id');
        $sub_order_check = $this->suborder()->subOrderCheck($suborder, $restaurant_id);

        if (!$sub_order_check) {
            return response()->json([
                "success" => false,
                "message" => "Order doesn't exist!"
            ]);
        }

        $orders = $this->suborder()->where('id', $suborder)->get()->first();

        if ($orders['status'] != 2) {
            if ($orders['status'] == 4) {
                return response()->json([
                    "success" => false,
                    "message" => "Order already rejected!"
                ]);
            } else if ($orders['status'] == 3) {
                return response()->json([
                    "success" => false,
                    "message" => "Order already completed!"
                ]);
            } else {
                return response()->json([
                    "success" => false,
                    "message" => "Order already accepted!"
                ]);
            }
        }

        $accept = $this->suborder()->find($suborder)->update([
            "status" => 3
        ]);

        $status = $this->order()->updateStatus($suborder);

        $this->suborder()->find($suborder)->order()->update([
            "status" => $status
        ]);

        $sub_order = $this->suborder()->find($suborder)->order()->get()->first();

        $order = $this->suborder()->find($suborder)->order()->first();
        $customer = $this->order()->find($order['id'])->customer()->first();
        $user = $this->customer()->find($customer['id'])->user()->first();
        $sub_order = $this->suborder()->find($suborder)->get()->first();
        $restaurant_name = auth()->user()->restaurant()->value('name');
        //$this->user()->find($user['id'])->notify(new CompleteOrder($order['code'], $restaurant_name));

        if ($request->header()['origin'][0] == "app") {
            $this->logs()->create([
                "user_id" => auth()->user()->id,
                "ip_address" => $request->ip(),
                "type" => "Update",
                "description" => "Order #".$sub_order['code']." completed",
                "origin" => $request->header()['origin'][0]
            ]);
        }

        return response()->json([
            "success" => true,
            "data" => [
                "order_code" => $sub_order['code']
            ],
            "message" => "Successfully completed order!"
        ]);
    }
}
