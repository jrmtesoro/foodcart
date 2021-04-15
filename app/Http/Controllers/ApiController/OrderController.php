<?php

namespace App\Http\Controllers\ApiController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Mail\OrderConfirmation;
use Illuminate\Support\Facades\Mail;
use App\Order;
use App\Notifications\OwnerOrderConfirmation;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $customer = auth()->user()->customer();

        $customer_id = $customer->value('id');

        $orders = $this->customer()->find($customer_id)->order()->select(['code', 'status', 'created_at'])->orderBy('created_at', 'DESC');

        if ($request->has('status')) {
            if ($request->get('status') !== null) {
                $status = $request->get('status');
                if ($status == "processing") {
                    $orders->whereIn('orders.status', [0,1,2]);
                } else if ($status == "completed") {
                    $orders->whereIn('orders.status', [3,4,5]);
                }
            }
        }

        $orders = $orders->get();

        if ($orders->count() == 0) {
            return response()->json([
                "success" => false,
                "message" => "No orders found."
            ]);
        }

        foreach ($orders as $key => $value) {
            $temp = $value['created_at']->format('l, F d, Y h:i A');
            unset($orders[$key]['created_at']);
            $orders[$key]['date'] = $temp;
        }

        return response()->json([
            "success" => true,
            "data" => $orders
        ]);
    }

    public function show($code, Request $request)
    {
        $order = $this->order()->where('code', $code)->exists();
        
        if (!$order) {
            return response()->json([
                "success" => false,
                "message" => "No order found."
            ]);
        }

        $order = $this->order()->where('code', $code)->with(['suborder' => function ($query) {
            $query->with(['itemlist']);
        }])->get()->first();
        $order['date'] = $order->created_at->format('F d, Y h:i A');

        $indx = 0;
        foreach ($order['suborder'] as $suborder) {
            $rest = $this->suborder()->find($suborder['id'])->restaurant()->select(['id', 'slug', 'name', 'flat_rate', 'eta', 'contact_number'])->get()->first();
            $order['suborder'][$indx]['restaurant'] = $rest;
            $indx++;
        }

        return response()->json([
            "success" => true,
            "data" => $order
        ]);
    }

    public function store(Request $request)
    {
        $customer = auth()->user()->customer();

        $customer_id = $customer->value('id');

        $restaurants = $this->cart()->cartRestaurant($customer_id);

        $grand_total = 0;
        $cooking_time = 0;
        $rules = [];
        $slugs = [];
        $sub_totals = [];
        $restaurant_names = [];
        foreach ($restaurants as $restaurant) {
            $sub_total = 0;
            $sub_total += $restaurant->flat_rate;
            $grand_total += $restaurant->flat_rate;
            $cart_menu = $this->cart()->cartMenu($restaurant->slug, $customer_id);
            $temp = 0;
            foreach ($cart_menu as $menu) {
                $multiplier = ceil($menu->quantity/5);
                $temp += $menu->cooking_time*$multiplier;
                $grand_total += $menu->price*$menu->quantity;
                $sub_total += $menu->price*$menu->quantity;
            }

            $rules[$restaurant->slug] = 'required|numeric|not_in:0';

            $temp += $restaurant->eta;

            if ($temp > $cooking_time) {
                $cooking_time = $temp;
            }

            $slugs[] = $restaurant->slug;
            $sub_totals[$restaurant->slug] = $sub_total;
            $restaurant_names[$restaurant->slug] = $restaurant->name;
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => "Invalid Input",
                "errors" => $validator->errors()
            ]);
        }

        $error = [];

        foreach ($request->all() as $slug => $change) {
            if (in_array($slug, $slugs)) {
                if ($change < $sub_totals[$slug]) {
                    $error[$slug] = ["Insufficient Money for ".$restaurant_names[$slug]];
                }
            }
        }

        if (count($error) != 0) {
            return response()->json([
                "success" => false,
                "message" => "Invalid Input",
                "errors" => $error
            ]);
        }

        $code = strVal($this->order()->max('id')+1);

        $order = $this->customer()->find($customer_id)->order()->create([
            "total" => $grand_total,
            "cooking_time_total" => $cooking_time,
            "status" => 0,
            "code" => $code
        ]);

        foreach ($restaurants as $restaurant) {
            $cart_menu = $this->cart()->cartMenu($restaurant->slug, $customer_id);
            $temp = 0;
            $price = 0;
            foreach ($cart_menu as $menu) {
                $multiplier = ceil($menu->quantity/5);
                $temp += $menu->cooking_time*$multiplier;
                $price += $menu->price*$menu->quantity;
            }

            $temp += $restaurant->eta;

            $insert_arr = array(
                "total" => $price,
                "cooking_time_total" => $temp,
                "status" => 0,
                "payment" => $request->get($restaurant->slug)
            );

            if ($request->header()['origin'][0] == "app") {
                $insert_arr['origin'] = "app";
            }

            $suborder = $this->order()->find($order->id)->suborder()->create($insert_arr);

            $restaurant_id = $restaurant->id;

            $this->restaurant()->find($restaurant_id)->suborder()->save($suborder);

            $menus = $this->cart()->cartCheckout($customer_id, $restaurant_id);

            foreach ($menus as $menu) {
                $menu_id = $menu->id;

                $itemlist = $this->suborder()->find($suborder->id)->itemlist()->create([
                    "name" => $menu->name,
                    "price" => $menu->price,
                    "cooking_time" => $menu->cooking_time,
                    "quantity" => $menu->quantity
                ]);

                $identifier = "";
                $item_lists = $this->itemlist()->getIdentifier($menu_id, $restaurant_id);
                foreach ($item_lists as $item_list) {
                    if ($item_list->name == $itemlist->name ) {
                        $identifier = $item_list->identifier;
                    }
                }

                if ($identifier == "") {
                    do {
                        $identifier = str_random(6);
                        $duplicate_check = $this->itemlist()->where('identifier', $identifier)->get()->first();
                    } while($duplicate_check);
                }

                $this->itemlist()->find($itemlist->id)->update([
                    "identifier" => $identifier
                ]);

                $this->menu()->find($menu->id)->itemlist()->save($itemlist);
                $this->restaurant()->find($restaurant_id)->itemlist()->save($itemlist);
            }

            $user = $this->restaurant()->find($restaurant_id)->user()->get()->first();
            $user_id = $user['id'];

            //$this->user()->find($user_id)->notify(new OwnerOrderConfirmation($code));
        }

        $cart_items = $this->customer()->find($customer_id)->cart()->get();

        foreach ($cart_items as $item) {
            $menus = $this->cart()->find($item->id)->menu()->get();
            foreach ($menus as $menu) {
                $this->cart()->find($item->id)->menu()->detach($menu->id);
            }

            $this->cart()->find($item->id)->customer()->detach($customer_id);
            $this->cart()->find($item->id)->delete();
        }

        $email = auth()->user()->email;

        $order_id = $order->id;
        $order = $this->order()->find($order_id)->get()->first();
        $sub_orders = $this->order()->find($order_id)->suborder()->get();
        $created_at = $order['created_at'];
        $order['date'] = $created_at->format('F d, Y h:i A');
        $customer = $this->order()->find($order_id)->customer()->get()->first();
        
        $indx = 0;
        $restaurant_list = [];
        foreach ($sub_orders as $sub_order) {
            $sub_order_id = $sub_order->id;
            $item_list = $this->suborder()->find($sub_order_id)->itemlist()->get();
            $restaurant = $this->suborder()->find($sub_order_id)->restaurant()->get()->first();

            $restaurant_list[$indx] = $sub_order;
            $restaurant_list[$indx]['restaurant'] = $restaurant;
            $restaurant_list[$indx]['item_list'] = $item_list;
            $indx++;
        }

        $order1 = array(
            "restaurant_list" => $restaurant_list,
            "order" => $order,
            "grand_total" => $grand_total,
            "customer" => $customer
        );

        //Mail::to($email)->send(new OrderConfirmation($order1));

        if ($request->header()['origin'][0] == "app") {
            $this->logs()->create([
                "user_id" => auth()->user()->id,
                "ip_address" => $request->ip(),
                "type" => "Insert",
                "description" => "Placed order #".$order1['order']['code'],
                "origin" => $request->header()['origin'][0]
            ]);
        }

        return response()->json([
            "success" => true,
            "data" => [
                "order_code" => $order1['order']['code']
            ],
            "message" => "Successfully submitted order!"
        ]);
    }
}
