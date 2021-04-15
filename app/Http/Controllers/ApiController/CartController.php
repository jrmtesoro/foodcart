<?php

namespace App\Http\Controllers\ApiController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class CartController extends Controller
{
    public function index()
    {
        $customer_id = auth()->user()->customer()->value('id');
        
        $user_cart = $this->customer()->find($customer_id)->cart()->get();
        foreach ($user_cart as $cart_item) {
            if (Carbon::parse($cart_item->updated_at)->addMinutes(60)->isPast()) {
                $menu = $this->cart()->find($cart_item->id)->menu()->get()->first();
                $this->cart()->find($cart_item->id)->menu()->detach($menu['id']);
                $this->cart()->find($cart_item->id)->customer()->detach($customer_id);
                $this->cart()->find($cart_item->id)->delete();
            }
        }

        $restaurant = $this->cart()->cartRestaurant($customer_id);
        if ($restaurant->count() == 0) {
            return response()->json([
                "success" => false,
                "message" => "Cart is empty."
            ]);
        }

        $restaurant_list = [];

        $indx = 0;
        foreach ($restaurant as $rest) {
            $menu = $this->cart()->cartMenu($rest->slug, $customer_id);
            $restaurant_list[$indx]['name'] = $rest->name;
            $restaurant_list[$indx]['flat_rate'] = $rest->flat_rate;
            $restaurant_list[$indx]['eta'] = $rest->eta;
            $restaurant_list[$indx]['slug'] = $rest->slug;
            $restaurant_list[$indx]['menu'] = $menu->toArray();
            $indx++;
        }

        return response()->json([
            "success" => true,
            "data" => $restaurant_list
        ]);
    }

    public function store(Request $request)
    {
        $customer_id = auth()->user()->customer()->value('id');
        $user_cart = $this->customer()->find($customer_id)->cart()->get();
        foreach ($user_cart as $cart_item) {
            if (Carbon::parse($cart_item->updated_at)->addMinutes(60)->isPast()) {
                $menu = $this->cart()->find($cart_item->id)->menu()->get()->first();
                $this->cart()->find($cart_item->id)->menu()->detach($menu['id']);
                $this->cart()->find($cart_item->id)->customer()->detach($customer_id);
                $this->cart()->find($cart_item->id)->delete();
            }
        }
        
        if (!$request->has('menu_slug')) {
            return response()->json([
                "success" => false,
                "message" => "Menu ID does not exist!"
            ]);
        }
        
        $menu_slug = $request->get('menu_slug');
        $menu = $this->menu()->where('slug', $menu_slug)->get()->first();

        if (!$menu) {
            return response()->json([
                "success" => false,
                "message" => "Menu ID does not exist!"
            ]);
        }

        if (!$request->has('quantity')) {
            return response()->json([
                "success" => false,
                "message" => "Please enter the quantity!"
            ]);
        }

        $menu_det = $this->menu()->where('slug', $menu_slug)->get()->first();
        $restaurant = $this->menu()->find($menu_det['id'])->restaurant()->get()->first();

        $open_time = Carbon::createFromFormat('H:i:s', $restaurant['open_time']);
        $close_time = Carbon::createFromFormat('H:i:s', $restaurant['close_time']);
        $now = Carbon::now();

        $open = false;
        if ($open_time->greaterThan($close_time)) {
            $open = ($now->greaterThanOrEqualTo($open_time) && $now->lessThan($close_time->addDay()));
        } else if ($restaurant['open_time'] == $restaurant['close_time']) {
            $open = true;
        } else {
            $open = ($now->lessThan($close_time) && $now->greaterThan($open_time));
        }

        if (!$open) {
            return response()->json([
                "success" => false,
                "message" => "The store is closed. Please pick from another restaurant."
            ]);
        }

        $menu_details = $menu->toArray();
        $menu_id = $menu_details['id'];

        $values = array(
            "quantity" => $request->get('quantity')
        );

        $customer_id = auth()->user()->customer()->value('id');

        $check = $this->cart()->cartCheck($customer_id, $menu_slug);

        if ($check) {
            $cart_id = $check->id;
            $cart = $this->customer()->find($customer_id)->cart()->find($cart_id)->increment('quantity', $request->get('quantity'));
            
            if ($request->header()['origin'][0] == "app") {
                $this->logs()->create([
                    "user_id" => auth()->user()->id,
                    "ip_address" => $request->ip(),
                    "type" => "Update",
                    "description" => "Updated Menu Slug : ".$request->get('menu_slug')." in cart",
                    "origin" => $request->header()['origin'][0]
                ]);
            }

            return response()->json([
                "success" => true,
                "message" => "Item has been successfully added into the cart!"
            ]);
        } 

        $cart = $this->cart()->create($values);
        
        $customer_cart = $this->customer()->find($customer_id)->cart()->save($cart);
        $menu_cart = $this->menu()->find($menu_id)->cart()->save($cart);

        if ($request->header()['origin'][0] == "app") {
            $this->logs()->create([
                "user_id" => auth()->user()->id,
                "ip_address" => $request->ip(),
                "type" => "Insert",
                "description" => "Added Menu Slug : ".$request->get('menu_slug')." to cart",
                "origin" => $request->header()['origin'][0]
            ]);
        }

        return response()->json([
            "success" => true,
            "message" => "Item has been successfully added into the cart!"
        ]);
    }

    public function show()
    {
        $customer_id = auth()->user()->customer()->value('id');

        // return $this->cart()->cartRestaurant($customer_id);
        
        $cart = $this->customer()->find($customer_id)->cart()->get();

        if ($cart->count() == 0) {
            return response()->json([
                "success" => false,
                "message" => "Cart is empty!"
            ]);
        }

        return response()->json([
            "success" => true,
            "data" => $cart
        ]);
    }

    public function update($cart, Request $request)
    {
        $customer_id = auth()->user()->customer()->value('id');
        $user_cart = $this->customer()->find($customer_id)->cart()->get();
        foreach ($user_cart as $cart_item) {
            if (Carbon::parse($cart_item->updated_at)->addMinutes(60)->isPast()) {
                $menu = $this->cart()->find($cart_item->id)->menu()->get()->first();
                $this->cart()->find($cart_item->id)->menu()->detach($menu['id']);
                $this->cart()->find($cart_item->id)->customer()->detach($customer_id);
                $this->cart()->find($cart_item->id)->delete();
            }
        }

        $validator = Validator::make($request->all(), [
            'cart_id' => 'required|exists:cart,id',
            'quantity' => 'required|numeric|digits_between:1,4|min:0|not_in:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => "Invalid Input",
                "errors" => $validator->errors()
            ]);
        }

        $customer_id = auth()->user()->customer()->value('id');

        $check = $this->customer()->find($customer_id)->cart()->find($cart)->get();

        if ($check->count() == 0) {
            return response()->json([
                "success" => false,
                "message" => "Item does not exist!"
            ]);
        }

        $cart = $this->cart()->find($cart)->update(['quantity' => $request->get('quantity')]);

        if ($request->header()['origin'][0] == "app") {
            $this->logs()->create([
                "user_id" => auth()->user()->id,
                "ip_address" => $request->ip(),
                "type" => "Insert",
                "description" => "Updated Quantity of Menu Slug : ".$request->get('menu_slug')." in cart",
                "origin" => $request->header()['origin'][0]
            ]);
        }

        return response()->json([
            "success" => true,
            "message" => "Cart Item updated!"
        ]);
    }

    public function empty()
    {
        $customer_id = auth()->user()->customer()->value('id');

        $cart = $this->customer()->find($customer_id)->cart()->get();

        if ($cart->count() == 0) {
            return response()->json([
                "success" => false,
                "message" => "Cart is empty!"
            ]);
        }

        $cart_array = $cart->toArray();

        $this->customer()->find($customer_id)->cart()->detach();

        foreach ($cart_array as $row) {
            $cart_id = $row['id'];
            $this->cart()->find($cart_id)->menu()->detach();
            $this->cart()->find($cart_id)->delete();
        }

        return response()->json([
            "success" => true,
            "message" => "The cart has been emptied"
        ]);
    }

    public function destroy($cart)
    {
        $menu = $this->cart()->find($cart)->menu()->get()->first();
        $menu_id = $menu->id;

        $this->menu()->find($menu_id)->cart()->detach();
        $this->cart()->find($cart)->customer()->detach();
        $this->cart()->find($cart)->delete();

        return response()->json([
            "success" => true,
            "message" => "Item successfully deleted!"
        ]);
    }
}
