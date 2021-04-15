<?php

namespace App\Http\Controllers\ApiController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $customer = $this->customer()->get();

        return response()->json([
            "success" => true,
            "data" => $customer
        ]);
    }

    public function show($customer_id, Request $request)
    {
        $customer = $this->customer()->where('id', $customer_id)->get()->first();

        if (!$customer) {
            return response()->json([
                "success" => false,
                "message" => "Customer does not exist!"
            ]);
        }

        $orders = $this->customer()->find($customer_id)->order()
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

        $reports = $this->customer()->find($customer_id)->report()
                ->select(['report.id','report.code', 'report.status','report.created_at'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

        $user = $this->customer()->find($customer_id)->user()->get()->first();
        $user_id = $user['id'];   

        $logs = $this->logs()->where('user_id', $user_id)
                ->select(['type','description','created_at'])
                ->orderBy('logs.created_at', 'DESC')
                ->limit(5)
                ->get();

        $customer['date'] = Carbon::parse($customer['created_at'])->format('F d, Y h:i A');

        $user = $this->customer()->find($customer_id)->user()->get()->first();
        $user_id = $user['id'];

        $ban = $this->user()->find($user_id)->ban()->get()->first();
        $customer['banned'] = false;
        if ($ban) {
            $customer['banned'] = true;
        }

        return response()->json([
            "success" => true,
            "data" => $customer,
            "orders" => $orders,
            "reports" => $reports,
            "logs" => $logs,
        ]);
    }

    public function edit()
    {
        $user_id = auth()->user()->id;
        $customer_id = auth()->user()->customer()->value('id');

        $user = $this->user()->where('id', $user_id)->select(['id', 'email'])->get()->first();
        $customer = $this->customer()->where('id', $customer_id)->select(['id', 'fname', 'lname', 'address', 'contact_number'])->get()->first();

        $user['customer'] = $customer;

        return response()->json([
            "success" => true,
            "data" => $user
        ]);
    }

    public function customer_order($customer_id, $order_code, Request $request)
    {
        $customer = $this->customer()->where('id', $customer_id)->get()->first();
        if (!$customer) {
            return response()->json([
                "success" => false,
                "message" => "Customer does not exist!"
            ]);
        }

        $order = $this->customer()->find($customer_id)->order()->where('orders.code', $order_code)->get()->first();
        
        if (!$order) {
            return response()->json([
                "success" => false,
                "message" => "Order does not exist!"
            ]);
        }

        $order = $this->order()->where('code', $order_code)->with(['suborder' => function ($query) {
            $query->with(['itemlist']);
        }])->get()->first();
        $order['date'] = $order->created_at->format('F d, Y h:i A');

        $indx = 0;
        foreach ($order['suborder'] as $suborder) {
            $rest = $this->suborder()->find($suborder['id'])->restaurant()->select(['id', 'slug', 'name', 'flat_rate', 'eta'])->get()->first();
            $order['suborder'][$indx]['restaurant'] = $rest;
            $indx++;
        }

        return response()->json([
            "success" => true,
            "data" => $order
        ]);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reg_fname' => 'required|min:3|max:30',
            'reg_lname' => 'required|min:3|max:30',
            'reg_contact_number' => 'required|min:7|max:11',
            'reg_address' => 'required',
            'reg_password' => 'min:8|max:21',
            'reg_password_confirm' => 'min:8|max:21|same:reg_password'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => "Invalid Input",
                "errors" => $validator->errors()
            ]);
        }

        if ($request->has('reg_password')) {
            $user = auth()->user();

            $this->user()->where('id', $user['id'])->update([
                "password" => bcrypt($request->get('reg_password'))
            ]);
        }

        $customer_info = array(
            "fname" => $request->get('reg_fname'),
            "lname" => $request->get('reg_lname'),
            "contact_number" => $request->get('reg_contact_number'),
            "address" => $request->get('reg_address')
        );

        $customer_id = auth()->user()->customer()->value('id');

        $this->customer()->find($customer_id)->update($customer_info);

        if ($request->header()['origin'][0] == "app") {
            $this->logs()->create([
                "user_id" => auth()->user()->id,
                "ip_address" => $request->ip(),
                "type" => "Update",
                "description" => "Update Profile",
                "origin" => $request->header()['origin'][0]
            ]);
        }
        
        return response()->json([
            "success" => true,
            "message" => "Successfully updated profile!"
        ]);
    }

    public function cancel_suborder(Request $request) {
        $suborder_id = $request->get('sub_order_id');
        $suborder_update = $this->suborder()->where("id", $suborder_id)->update([
            "status" => 5
        ]);

        return response()->json([
            "success" => true,
            "message" => "Successfully cancelled suborder!"
        ]);
    }

    public function cancel_order(Request $request) {
        $order_code = $request->get('order_code');
        $order_update = $this->order()->where('code', $order_code)->update([
            "status" => 5
        ]);

        $sub_orders = \DB::table('orders')->select(['sub_orders.id'])
                ->join('order_sub_order', 'order_sub_order.order_id', '=', 'orders.id')
                ->join('sub_orders', 'sub_orders.id', '=', 'order_sub_order.sub_order_id')
                ->where('orders.code', $order_code)
                ->get();

        foreach ($sub_orders as $sub_order) {
            $sub_order_cancel = $this->suborder()->where('id', $sub_order->id)->update([
                "status" => 5
            ]);
        }

        return response()->json([
            "success" => true,
            "message" => "Successfully cancelled order!"
        ]);
    }
}
