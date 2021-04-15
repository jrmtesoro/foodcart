<?php

namespace App\Http\Controllers\ApiController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

use App\Mail\OrderConfirmation;
use Illuminate\Support\Facades\Mail;

class GuestController extends Controller
{
    public function home(Request $request)
    {
        $restaurants = $this->restaurant()
                    ->whereNotNull('flat_rate')
                    ->whereNotNull('eta')
                    ->whereNotNull('open_time')
                    ->whereNotNull('close_time');

        if ($request->has('search')) {
            $search = $request->get('search');
            $restaurants = $this->restaurant()->where("(restaurant.name", "LIKE" , "{%$search%}");
        }

        $restaurants = $restaurants->orderBy('created_at', 'DESC')
                    ->get();

        $now = Carbon::now();
        $indx = 0;
        foreach ($restaurants as $restaurant) {
            $menu = $this->restaurant()->find($restaurant['id'])->menu()->get()->first();

            $user = $this->restaurant()->find($restaurant['id'])->user()->get()->first();
            $ban = $this->user()->find($user['id'])->ban()->get()->first();
            if ($ban || !$menu) {
                unset($restaurants[$indx]);
            } else {
                $open_time = Carbon::parse($restaurant['open_time']);
                $close_time = Carbon::parse($restaurant['close_time']);

                $open = false;
                if ($open_time->greaterThan($close_time)) {
                    $open = ($now->greaterThanOrEqualTo($open_time) && $now->lessThan($close_time->addDay()));
                } else if ($restaurant['open_time'] == $restaurant['close_time']) {
                    $open = true;
                } else {
                    $open = ($now->lessThan($close_time) && $now->greaterThan($open_time));
                }

                $restaurants[$indx]['rating'] = round($restaurant['rating'], 1);
                
                $times = $open_time->format('h:i A').' - '.$close_time->format('h:i A');
                if ($restaurant['open_time'] == $restaurant['close_time']) {
                    $times = "24 HOURS";
                }

                $restaurants[$indx]['times'] = $times;

                $restaurants[$indx]['open'] = $open; 
            }
            $indx++;
        }

        $rests = $restaurants;

        $count = $restaurants->count();
    
        $max = ($count > 5 ? 5 : $count);
        $newly_added = [];
        $whats_hot = [];
        $top_rated = [];
        
        $newly_added_id = [];

        $indx = 1;
        foreach ($restaurants as $restaurant) {
            $newly_added[] = $restaurant;
            if ($max == $indx) {
                break;
            }
            $indx++;
        }

        foreach ($newly_added as $rest) {
            foreach ($restaurants as $key1 => $value1) {
                if ($value1['id'] == $rest['id']) {
                    $newly_added_id[] = $rest['id'];
                    unset($restaurants[$key1]);
                    break;
                }
            }
        }
        
        $count = $restaurants->count();
        $max = ($count > 5 ? 5 : $count);
        $indx = 1;
        if ($max != 0) {
            $hot = [];
            foreach ($restaurants as $restaurant) { 
                $sub_order_count = \DB::table('sub_orders')->select('sub_orders.id')
                        ->join('restaurant_sub_order', 'restaurant_sub_order.sub_order_id', '=', 'sub_orders.id')
                        ->join('restaurant', 'restaurant.id', '=', 'restaurant_sub_order.restaurant_id')
                        ->where('restaurant.id', $restaurant['id'])
                        ->where('sub_orders.status', 3)
                        ->get()->count();

                if ($sub_order_count != 0) {
                    $hot[$restaurant['id']] = $sub_order_count;
                    $indx++;
                }
                if ($indx == $max) {
                    break;
                }
            }
            arsort($hot);
            foreach ($hot as $key => $value) {
                foreach ($restaurants as $key1 => $value1) {
                    if ($value1['id'] == $key) {
                        $whats_hot[] = $value1;
                        unset($restaurants[$key1]);
                        break;
                    }
                }
            }
        }

        $restaurants1 = $this->restaurant()
                    ->whereNotNull('flat_rate')
                    ->whereNotNull('eta')
                    ->whereNotNull('open_time')
                    ->whereNotNull('close_time');

        if ($request->has('search')) {
            $search = $request->get('search');
            $restaurants1 = $this->restaurant()->where("(restaurant.name", "LIKE" , "{%$search%}");
        }

        $restaurants1 = $restaurants1->orderBy('rating', 'DESC')->get();

        $now = Carbon::now();
        $indx = 0;
        foreach ($restaurants1 as $restaurant) {
            $menu = $this->restaurant()->find($restaurant['id'])->menu()->get()->first();

            $user = $this->restaurant()->find($restaurant['id'])->user()->get()->first();
            $ban = $this->user()->find($user['id'])->ban()->get()->first();
            if ($ban || !$menu) {
                unset($restaurants1[$indx]);
            } else {
                $open_time = Carbon::parse($restaurant['open_time']);
                $close_time = Carbon::parse($restaurant['close_time']);

                $open = false;
                if ($open_time->greaterThan($close_time)) {
                    $open = ($now->greaterThanOrEqualTo($open_time) && $now->lessThan($close_time->addDay()));
                } else if ($restaurant['open_time'] == $restaurant['close_time']) {
                    $open = true;
                } else {
                    $open = ($now->lessThan($close_time) && $now->greaterThan($open_time));
                }

                $restaurants1[$indx]['rating'] = round($restaurant['rating'], 1);
                
                $times = $open_time->format('h:i A').' - '.$close_time->format('h:i A');
                if ($restaurant['open_time'] == $restaurant['close_time']) {
                    $times = "24 HOURS";
                }

                $restaurants1[$indx]['times'] = $times;

                $restaurants1[$indx]['open'] = $open; 
            }
            $indx++;
        }

        $count = $restaurants1->count();
        $max = ($count > 5 ? 5 : $count);
        $indx = 1;
        if ($max != 0) {
            $rated = [];
            foreach($restaurants1 as $rest) {
                if ($rest['rating'] != 0) {
                    $rated[$rest['id']] = $rest['rating'];
                    $indx++;
                }
                if ($indx == $max) {
                    break;
                }
            }
    
            arsort($rated);
            foreach ($rated as $key => $value) {
                foreach ($restaurants1 as $key1 => $value1) {
                    if ($value1['id'] == $key) {
                        $top_rated[] = $value1;
                        unset($restaurants1[$key1]);
                        break;
                    }
                }
            }
        }
    
        return response()->json([
            "success" => true,
            "data" => [
                "whats_hot" => $whats_hot,
                "top_rated" => $top_rated,
                "newly_added" => $newly_added
            ]
        ]);
    }
    
    public function checkout(Request $request)
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
                "success" => false
            ]);
        }

        $restaurant_list = [];

        $indx = 0;
        $grand_total = 0;
        $total_cooking_time = 0;
        $total_flat_rate = 0;
        $validation_rules = [];
        $validation_message = [];
        foreach ($restaurant as $rest) {
            $temp = 0;
            $total = 0;
            $menu = $this->cart()->cartMenu($rest->slug, $customer_id);
            $restaurant_list[$indx]['name'] = $rest->name;
            $restaurant_list[$indx]['flat_rate'] = $rest->flat_rate;
            $restaurant_list[$indx]['eta'] = $rest->eta;
            $restaurant_list[$indx]['slug'] = $rest->slug;
            $restaurant_list[$indx]['contact_number'] = $rest->contact_number;

            $validation_rules[$rest->slug] = array(
                "required" => true,
                "digits" => true,
                $rest->slug => true
            );

            $validation_message[$rest->slug] = array(
                "required" => "Enter the amount of cash you're paying",
                "digits" => "The amount must be numerical",
                $rest->slug => "Insufficient Money"
            );
            
            $menu_array = $menu->toArray();

            $total_flat_rate += $rest->flat_rate;   

            $restaurant_list[$indx]['menu'] = $menu_array;

            $temp = 0;
            foreach ($menu_array as $m) {
                $price = $m->price;
                $cooking_time = $m->cooking_time;

                $multiplier = ceil($m->quantity/5);
                $temp += $cooking_time * $multiplier;
                $total += $price * $m->quantity;
            }

            $total += $rest->flat_rate;

            $temp += $rest->eta;

            $restaurant_list[$indx]['sub_eta'] = $temp;

            if ($total_cooking_time < $temp) {
                $total_cooking_time = $temp;
            }

            $restaurant_list[$indx]['total'] = $total;

            $grand_total += $total;

            $indx++;
        }
        
        return response()->json([
            "success" => true,
            "data" => $restaurant_list,
            "grand_total" => $grand_total,
            "total_cooking_time" => $total_cooking_time,
            "total_flat_rate" => $total_flat_rate,
            "validation_rules" => $validation_rules,
            "validation_message" => $validation_message
        ]);
    }

    public function guest_tag()
    {
        $tags = $this->tag()->where('status', 1)->get();

        return response()->json([
            "success" => true,
            "data" => $tags
        ]);
    }

    public function guest_restaurant($slug)
    {
        $restaurant = $this->restaurant()->where('slug', $slug)->exists();

        if (!$restaurant) {
            return response()->json([
                "success" => false,
                "message" => "Restaurant doesn't exist!"
            ]);
        }

        $category = $this->restaurant()->where('restaurant.slug', $slug)
            ->select(["restaurant.id", "restaurant.name",
             "restaurant.address", "restaurant.contact_number",
             "restaurant.image_name", "restaurant.slug",
             "restaurant.rating", "restaurant.open_time", "restaurant.close_time"])
            ->with(['category' => function($q) {
                $q->with(['menu' => function ($q1) {
                    $q1->with(['tag:name,status'])->select(["menu.id" , "menu.name", "menu.description", "menu.price", "menu.cooking_time", "menu.image_name", "menu.slug"])->whereNull('deleted_at');
                }])->select(["category.id" , "category.name"])->whereNull('deleted_at');
            }])->get()->first()->toArray();

        $rating = $category['rating'];

        $open_time = Carbon::createFromFormat('H:i:s', $category['open_time']);
        $close_time = Carbon::createFromFormat('H:i:s', $category['close_time']);
        $now = Carbon::now();

        $open = false;
        if ($open_time->greaterThan($close_time)) {
            $open = ($now->greaterThanOrEqualTo($open_time) && $now->lessThan($close_time->addDay()));
        } else if ($category['open_time'] == $category['close_time']) {
            $open = true;
        } else {
            $open = ($now->lessThan($close_time) && $now->greaterThan($open_time));
        }

        $category['open'] = $open;
        $times = $open_time->format('h:i A').' - '.$close_time->format('h:i A');
        if ($category['open_time'] == $category['close_time']) {
            $times = "24 HOURS";
        }
        $category['times'] = $times;


        $category['rating'] = round($category['rating'], 1);

        $votes = $this->restaurant()->find($category['id'])->rating()->get();

        $vote_count = 0;
        foreach ($votes as $vote) {
            $vote_count++;
        }

        $vote = "votes";
        if ($vote_count <= 1) {
            $vote = "vote";
        }
        $category['votes'] = "(".$vote_count." ".$vote.")";

        $indx = 0;
        foreach ($category['category'] as $c) {
            $category_menu = $this->category()->find($c['id'])->menu()->get()->first();
            if (!$category_menu) {
                unset($category['category'][$indx]);
            }
            $indx++;
        }

        return response()->json([
            "success" => true,
            'data' => $category
        ]);
    }

    public function home1(Request $request)
    {
        $customer_id = auth()->user()->customer()->value('id');
        $restaurants = $this->restaurant()
                    ->whereNotNull('flat_rate')
                    ->whereNotNull('eta')
                    ->whereNotNull('open_time')
                    ->whereNotNull('close_time');

        if ($request->has('search')) {
            $search = $request->get('search');
            $restaurants = $this->restaurant()->where("(restaurant.name", "LIKE" , "{%$search%}");
        }


        $restaurant_compilation = [];

        $restaurants = $restaurants->orderBy('created_at', 'DESC')
                    ->get();

        $now = Carbon::now();
        $indx = 0;
        foreach ($restaurants as $restaurant) {
            $menu = $this->restaurant()->find($restaurant['id'])->menu()->get()->first();

            $user = $this->restaurant()->find($restaurant['id'])->user()->get()->first();
            $ban = $this->user()->find($user['id'])->ban()->get()->first();
            if ($ban || !$menu) {
                unset($restaurants[$indx]);
            } else {
                $open_time = Carbon::parse($restaurant['open_time']);
                $close_time = Carbon::parse($restaurant['close_time']);

                $open = false;
                if ($open_time->greaterThan($close_time)) {
                    $open = ($now->greaterThanOrEqualTo($open_time) && $now->lessThan($close_time->addDay()));
                } else if ($restaurant['open_time'] == $restaurant['close_time']) {
                    $open = true;
                } else {
                    $open = ($now->lessThan($close_time) && $now->greaterThan($open_time));
                }

                $restaurants[$indx]['rating'] = round($restaurant['rating'], 1);
                
                $times = $open_time->format('h:i A').' - '.$close_time->format('h:i A');
                if ($restaurant['open_time'] == $restaurant['close_time']) {
                    $times = "24 HOURS";
                }

                $restaurants[$indx]['times'] = $times;

                $restaurants[$indx]['open'] = $open; 
            }
            $indx++;
        }

        $other_customer_restaurant = [];
        $customer_restaurants = \DB::table('sub_orders')->select('restaurant.id')
                ->join('order_sub_order', 'order_sub_order.sub_order_id', '=', 'sub_orders.id')
                ->join('orders', 'orders.id', '=', 'order_sub_order.order_id')
                ->join('customer_order', 'customer_order.order_id', '=', 'orders.id')
                ->join('customer', 'customer.id', '=', 'customer_order.customer_id')
                ->join('restaurant_sub_order', 'restaurant_sub_order.sub_order_id', '=', 'sub_orders.id')
                ->join('restaurant', 'restaurant.id', '=', 'restaurant_sub_order.restaurant_id')
                ->where('customer.id', $customer_id)
                ->where('orders.status', 3)
                ->get();

        $score_array = [];
        
        if ($customer_restaurants->count() != 0) {
            $customers = \DB::table('customer')->select('customer.id')
                ->join('customer_order', 'customer_order.customer_id', '=', 'customer.id')
                ->join('orders', 'orders.id', '=', 'customer_order.order_id')
                ->join('order_sub_order', 'order_sub_order.order_id', '=', 'orders.id')
                ->join('sub_orders', 'sub_orders.id', '=', 'order_sub_order.sub_order_id')
                ->join('restaurant_sub_order', 'restaurant_sub_order.sub_order_id', '=', 'sub_orders.id')
                ->join('restaurant', 'restaurant.id', '=', 'restaurant_sub_order.restaurant_id')
                ->where('orders.status', 3);
                
            $i = 0;

            foreach ($customer_restaurants as $restaurant) {
                $restaurant_id = $restaurant->id;

                if ($i == 0) {
                    $customers->where('restaurant.id', $restaurant_id);
                } else {
                    $customers->orWhere('restaurant.id', $restaurant_id);
                }
                $i++;
            }
            
            $customers = $customers->get();

            //return response()->json($customers);

            $restaurant_array = [];

            foreach ($customers as $cust) {
                $cust_id = $cust->id;
                
                $other_customer_restaurant = \DB::table('sub_orders')->select('restaurant.id')
                        ->join('order_sub_order', 'order_sub_order.sub_order_id', '=', 'sub_orders.id')
                        ->join('orders', 'orders.id', '=', 'order_sub_order.order_id')
                        ->join('customer_order', 'customer_order.order_id', '=', 'orders.id')
                        ->join('customer', 'customer.id', '=', 'customer_order.customer_id')
                        ->join('restaurant_sub_order', 'restaurant_sub_order.sub_order_id', '=', 'sub_orders.id')
                        ->join('restaurant', 'restaurant.id', '=', 'restaurant_sub_order.restaurant_id')
                        ->where('customer.id', $cust_id)
                        ->where('orders.status', 3)
                        ->get();

                foreach ($other_customer_restaurant as $restaurant) {
                    $restaurant_id = $restaurant->id;
                    $similar_restaurant = false;
                    foreach ($customer_restaurants as $rest) {
                        if ($rest->id == $restaurant_id) {
                            $similar_restaurant = true;
                            break;
                        }
                    }

                    if (!$similar_restaurant) {
                        $restaurant_array[] = $restaurant_id;
                    }
                }
            }

            foreach ($restaurant_array as $restaurant_id) {
                if (!array_key_exists($restaurant_id, $score_array)) {
                    $score_array[$restaurant_id] = 0;
                }
                $score_array[$restaurant_id] += 1;
            }
            arsort($score_array);
        }
        // foreach ($customer_restaurants as $restaurant) {
        //     $restaurant_id = $restaurant->id;
            // foreach ($restaurant_customers as $cust) {
            //     $cust_id = $cust->id;
            //     $customer_restaurant = \DB::table('sub_orders')->select('restaurant.id')
            //         ->join('order_sub_order', 'order_sub_order.sub_order_id', '=', 'sub_orders.id')
            //         ->join('orders', 'orders.id', '=', 'order_sub_order.order_id')
            //         ->join('customer_order', 'customer_order.order_id', '=', 'orders.id')
            //         ->join('customer', 'customer.id', '=', 'customer_order.customer_id')
            //         ->join('restaurant_sub_order', 'restaurant_sub_order.sub_order_id', '=', 'sub_orders.id')
            //         ->join('restaurant', 'restaurant.id', '=', 'restaurant_sub_order.restaurant_id')
            //         ->where('customer.id', $cust_id)
            //         ->where('orders.status', 3)
            //         ->get();

            //     $other_customer_restaurant[] = $customer_restaurant;
            // }

            // foreach ($other_customer_restaurant as $customer_restaurant) {
            //     foreach ($customer_restaurant as $restaurant) {
            //         if (!in_array($restaurant->id, $restaurant_compilation)) {
            //             $restaurant_compilation[] = $restaurant->id;
            //         }
            //     }
            // }    
        // }
    
        $recommendation = []; 
        if (empty($score_array) || count($score_array) != 0) {
            $i = 0;
            foreach ($score_array as $key => $value) {
                foreach ($restaurants as $rest) {
                    if ($key == $rest->id) {
                        if ($i != 5) {
                            $recommendation[] = $rest;
                        }
                        $i++;
                    }
                }
            }    
        }

        $count = $restaurants->count();
    
        $max = ($count > 5 ? 5 : $count);
        $newly_added = [];
        $whats_hot = [];
        $top_rated = [];
        
        $newly_added_id = [];

        $indx = 1;
        foreach ($restaurants as $restaurant) {
            $newly_added[] = $restaurant;
            if ($max == $indx) {
                break;
            }
            $indx++;
        }

        foreach ($newly_added as $rest) {
            foreach ($restaurants as $key1 => $value1) {
                if ($value1['id'] == $rest['id']) {
                    $newly_added_id[] = $rest['id'];
                    unset($restaurants[$key1]);
                    break;
                }
            }
        }
        
        $count = $restaurants->count();
        $max = ($count > 5 ? 5 : $count);
        $indx = 1;
        if ($max != 0) {
            $hot = [];
            foreach ($restaurants as $restaurant) { 
                $sub_order_count = \DB::table('sub_orders')->select('sub_orders.id')
                        ->join('restaurant_sub_order', 'restaurant_sub_order.sub_order_id', '=', 'sub_orders.id')
                        ->join('restaurant', 'restaurant.id', '=', 'restaurant_sub_order.restaurant_id')
                        ->where('restaurant.id', $restaurant['id'])
                        ->where('sub_orders.status', 3)
                        ->get()->count();

                if ($sub_order_count != 0) {
                    $hot[$restaurant['id']] = $sub_order_count;
                    $indx++;
                }
                if ($indx == $max) {
                    break;
                }
            }
            arsort($hot);
            foreach ($hot as $key => $value) {
                foreach ($restaurants as $key1 => $value1) {
                    if ($value1['id'] == $key) {
                        $whats_hot[] = $value1;
                        unset($restaurants[$key1]);
                        break;
                    }
                }
            }
        }

        $restaurants1 = $this->restaurant()
                    ->whereNotNull('flat_rate')
                    ->whereNotNull('eta')
                    ->whereNotNull('open_time')
                    ->whereNotNull('close_time');

        if ($request->has('search')) {
            $search = $request->get('search');
            $restaurants1 = $this->restaurant()->where("(restaurant.name", "LIKE" , "{%$search%}");
        }

        $restaurants1 = $restaurants1->orderBy('rating', 'DESC')->get();

        $now = Carbon::now();
        $indx = 0;
        foreach ($restaurants1 as $restaurant) {
            $menu = $this->restaurant()->find($restaurant['id'])->menu()->get()->first();

            $user = $this->restaurant()->find($restaurant['id'])->user()->get()->first();
            $ban = $this->user()->find($user['id'])->ban()->get()->first();
            if ($ban || !$menu) {
                unset($restaurants1[$indx]);
            } else {
                $open_time = Carbon::parse($restaurant['open_time']);
                $close_time = Carbon::parse($restaurant['close_time']);

                $open = false;
                if ($open_time->greaterThan($close_time)) {
                    $open = ($now->greaterThanOrEqualTo($open_time) && $now->lessThan($close_time->addDay()));
                } else if ($restaurant['open_time'] == $restaurant['close_time']) {
                    $open = true;
                } else {
                    $open = ($now->lessThan($close_time) && $now->greaterThan($open_time));
                }

                $restaurants1[$indx]['rating'] = round($restaurant['rating'], 1);
                
                $times = $open_time->format('h:i A').' - '.$close_time->format('h:i A');
                if ($restaurant['open_time'] == $restaurant['close_time']) {
                    $times = "24 HOURS";
                }

                $restaurants1[$indx]['times'] = $times;

                $restaurants1[$indx]['open'] = $open; 
            }
            $indx++;
        }

        $count = $restaurants1->count();
        $max = ($count > 5 ? 5 : $count);
        $indx = 1;
        if ($max != 0) {
            $rated = [];
            foreach($restaurants1 as $rest) {
                if ($rest['rating'] != 0) {
                    $rated[$rest['id']] = $rest['rating'];
                    $indx++;
                }
                if ($indx == $max) {
                    break;
                }
            }
    
            arsort($rated);
            foreach ($rated as $key => $value) {
                foreach ($restaurants1 as $key1 => $value1) {
                    if ($value1['id'] == $key) {
                        $top_rated[] = $value1;
                        unset($restaurants1[$key1]);
                        break;
                    }
                }
            }
        }

        return response()->json([
            "success" => true,
            "data" => [
                "recommendation" => $recommendation,
                "whats_hot" => $whats_hot,
                "top_rated" => $top_rated,
                "newly_added" => $newly_added
            ]
        ]);
    }

    public function tester1(Request $request)
    {
        
    }

    public function changePass()
    {
        $users = $this->user()->get();

        foreach ($users as $user) {
            $this->user()->find($user->id)->update([
                "password" => bcrypt("123456")
            ]);
        }

        return "password changed!";
    }
}
