<?php

namespace App\Http\Controllers\ApiController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Notifications\RestaurantPartnership;
use Image;
use Illuminate\Http\File;
use Carbon\Carbon;
use function GuzzleHttp\json_decode;

class RestaurantController extends Controller
{
    public function index(Request $request)
    {
        $restaurant = $this->restaurant()->where('status', 1)->get();

        return response()->json([
            "success" => true,
            "data" => $restaurant
        ]);
    }
    
    public function restaurant_menu($restaurant_id, $menu_id, Request $request)
    {
        $restaurant = $this->restaurant()->find($restaurant_id)->get()->first();
        
        if (!$restaurant) {
            return response()->json([
                "success" => false,
                "message" => "Restaurant does not exist!"
            ]);
        }

        $menu = $this->menu()->find($menu_id)->get()->first();
        
        if (!$menu) {
            return response()->json([
                "success" => false,
                "message" => "Item does not exist!"
            ]);
        }

        $restaurant_menu = $this->restaurant()->find($restaurant_id)->menu()->where('menu.id', $menu_id)
                        ->with(['category', 'tag'])
                        ->get()->first();

        $restaurant_menu['restaurant_id'] = $restaurant_id;

        return response()->json([
            "success" => true,
            "data" => $restaurant_menu
        ]);
    }

    public function show($restaurant_id, Request $request)
    {
        $result = [];

        $restaurant = $this->restaurant()->where('id', $restaurant_id)->get()->first();
        
        if (!$restaurant) {
            return response()->json([
                "success" => false,
                "message" => "Restaurant does not exist!"
            ]);
        }

        $user = $this->restaurant()->find($restaurant_id)->user()->get()->first();

        if ($restaurant['open_time'] !== null || $restaurant['close_time'] !== null) {
            $open_time = Carbon::parse($restaurant['open_time'])->format('h:i A');
            $close_time = Carbon::parse($restaurant['close_time'])->format('h:i A');
            $restaurant['time'] = $open_time." - ".$close_time;
        }
        
        $result['restaurant'] = $restaurant;

        $category_list = $this->restaurant()->find($restaurant_id)->category()->pluck('name', 'id');

        $result['category_list'] = $category_list;

        $year = Carbon::now()->format('Y');
        $sub_orders = $this->restaurant()->totalOrders($restaurant_id, $year);
        $month_array = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $index = 0;
        $month_now = Carbon::now()->format('M');
        foreach ($month_array as $month) {
            $result['total_orders']['month'][$index] = $month;
            $result['total_orders']['data'][$index] = 0;
            foreach ($sub_orders as $m => $v) {
                if ($m == $month) {
                    foreach ($v as $order) {
                        $result['total_orders']['data'][$index]++;
                    }
                }
            }
            $index++;

            if ($month == $month_now) {
                break;
            }
        }
        
        $sub_orders1 = $this->restaurant()->totalSales($restaurant_id, $year);
        $month_array = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $index = 0;
        $month_now = Carbon::now()->format('M');     
        
        foreach ($month_array as $month) {
            $result['total_sales']['month'][$index] = $month;
            $result['total_sales']['data'][$index] = 0;
            foreach ($sub_orders1 as $m => $v) {
                if ($m == $month) {
                    foreach ($v as $order) {
                        $result['total_sales']['data'][$index] += $order->total;
                    }
                }
            }
            $index++;

            if ($month == $month_now) {
                break;
            }
        }

        $user = $this->restaurant()->find($restaurant_id)->user()->get()->first();
        $result['user_id'] = $user['id'];

        $result['banned'] = false;
        $ban = $this->user()->find($user['id'])->ban()->get()->first();
        if ($ban) {
            $result['banned'] = true;
        }

        $menu = $this->restaurant()->find($restaurant_id)->menu()->get();

        return response()->json([
            "success" => true,
            "data" => $result,
            "menu" => $menu,
            "user_id" => $user['id']
        ]);
        
    }

    public function search(Request $request)
    {
        $restaurants = $this->restaurant()
                    ->whereNotNull('flat_rate')
                    ->whereNotNull('eta')
                    ->whereNotNull('open_time')
                    ->whereNotNull('close_time');

        if ($request->has('search')) {
            $search = $request->get('search');
            $restaurants = $restaurants->where("name", "like", "%{$search}%");
        }

        if ($request->has('tag') && $request->get('tag') != "") {
            $tag_array = explode(',', $request->get('tag'));
            $restaurant_id_array = $restaurants->get()->pluck('id');

            $restaurants = $this->restaurant()->restaurantTagSearch($tag_array, $restaurant_id_array);
        } else {
            $restaurants = $restaurants->get();
        }

        $restaurants = json_decode($restaurants, true);

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

        $temp = $this->tag()->where('status', 1)->get()->pluck('name');

        $tags = [];
        $indx = 0;
        foreach ($temp as $tag) {
            $tags[$indx]['name'] = $tag;
            $indx++;
        }

        return response()->json([
            "success" => true,
            "data" => $restaurants,
            "tags" => $tags
        ]);
    }

    public function edit(Request $request)
    {
        $restaurant_id = auth()->user()->restaurant()->value('id');

        $user = $this->restaurant()->find($restaurant_id)->user()->first()->toArray();
        $user_id = $user['id'];

        $restaurant = $this->user()->find($user_id)->restaurant()->first()->toArray();

        $restaurant['open_time'] = Carbon::parse($restaurant['open_time'])->format('H:i');
        $restaurant['close_time'] = Carbon::parse($restaurant['close_time'])->format('H:i');

        return response()->json([
            "success" => true,
            "data" => [
                "user" => $user,
                "restaurant" => $restaurant
            ]
        ]);
    }

    public function update(Request $request)
    {
        $rules = [
            'contact_number' => 'required|min:7|max:11',
            'address' => 'required',
            'flat_rate' => 'required|numeric|digits_between:1,3|min:0|not_in:0',
            'image_name' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'eta' => 'required|numeric|digits_between:1,3|min:0|not_in:0'
        ];

        if (!$request->has('24hours')) {
            $rules['open_time'] = 'required';
            $rules['close_time'] = 'required';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => "Invalid Input",
                "errors" => $validator->errors()
            ]);
        }

        $restaurant_id = auth()->user()->restaurant()->value('id');

        $values = array(
            'contact_number' => $request->get('contact_number'),
            'address' => $request->get('address'),
            'flat_rate' => $request->get('flat_rate'),
            'eta' => $request->get('eta'),
            'open_time' => "00:00:00",
            'close_time' => "00:00:00"
        );

        if (!$request->has('24hours')) {
            $open_time = Carbon::createFromFormat('H:i a', $request->get('open_time'));
            $close_time = Carbon::createFromFormat('H:i a', $request->get('close_time'));
            
            if ($open_time->gte($close_time)) {
                return response()->json([
                    "success" => false,
                    "message" => "Invalid Input",
                    "errors" => [
                        "Opening/Closing Time" => [
                            "Please enter a valid opening/closing time."
                        ]
                    ]
                ]);
            }

            $values['open_time'] = $open_time->format('H:i:s');
            $values['close_time'] = $close_time->format('H:i:s');
        }

        if ($request->hasFile('image_name')) {
            $rest = $this->restaurant()->where('id', $restaurant_id)->first()->toArray();

            if (!empty($rest['image_name'])) {
                $file_name = $rest['image_name'];
                Storage::delete('restaurant/'.$file_name);
                Storage::delete('restaurant/medium/'.$file_name);
                Storage::delete('restaurant/thumbnail/'.$file_name);
            }

            $image = $request->file('image_name');
            $path = $image->getRealPath().'.jpg';
            $file_name = time().rand(1000, 9999).'.jpg';

            $whole_pic = Image::make($image)->encode('jpg')->save($path);
            Storage::putFileAs('restaurant', new File($path), $file_name);

            $medium = Image::make($image)->resize(300,200)->encode('jpg')->save($path);
            Storage::putFileAs('restaurant/medium', new File($path), $file_name);

            $thumbnail = Image::make($image)->resize(100, 100)->encode('jpg')->save($path);
            Storage::putFileAs('restaurant/thumbnail', new File($path), $file_name);

            $values['image_name'] = $file_name;
        }

        if ($request->header()['origin'][0] == "app") {
            $this->logs()->create([
                "user_id" => auth()->user()->id,
                "ip_address" => $request->ip(),
                "type" => "Update",
                "description" => "Restaurant Profile",
                "origin" => $request->header()['origin'][0]
            ]);
        }

        $restaurant = $this->restaurant()->find($restaurant_id)->update($values);

        return response()->json([
            'success' => true,
            'message' => 'Successfully updated restaurant profile!'
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reg_fname' => 'required|min:3|max:30',
            'reg_lname' => 'required|min:3|max:30',
            'reg_restaurant_name' => 'required|min:3|max:30',
            'reg_address' => 'required',
            'reg_contact_number' => 'required|min:7|max:11',
            'reg_email' => 'required|unique:user,email',
            'reg_permit_1' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'reg_permit_2' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'reg_permit_3' => 'nullable|image|mimes:jpeg,png,jpg|max:5120'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => "Invalid Input",
                "errors" => $validator->errors()
            ]);
        }

        $email = $request->get('reg_email');
        $user_check = $this->user()->where('email', '=', $email)->first();
        
        if ($user_check) {
            $restaurant_details = $this->user()->find($user_check->id)->restaurant()->first();
            $restaurant_array = $restaurant_details->toArray();
            $status = $restaurant_array['status'];
            $msg = "You already have already sent an application!";
            if ($status == 1) {
                $msg = "Your application has already been accepted!";
            } else if ($status == 2) {
                $msg = "Sorry, But your application to us before was declined.";
            }

            return response()->json([
                'success' => false,
                'message' => $msg
            ]);
        }
        
        $user_details = array(
            'email' => $request->get('reg_email'),
            'password' => bcrypt(Hash::make(str_random(20))),
            'access_level' => 2
        );

        $user = $this->user()->create($user_details);

        $restaurant_slug = str_slug($request->get('reg_restaurant_name'), '-');
        $all_slugs = $this->restaurant()->getRelatedSlugs($restaurant_slug);

        if ($all_slugs->contains('slug', $restaurant_slug)) {
            for ($i = 0; $i <= 100; $i++) {
                $temp = $restaurant_slug.'-'.$i;
                if (!$all_slugs->contains('slug', $temp)) {
                    $restaurant_slug = $temp;
                    break;
                }
            }
        }

        $restaurant_details = array(
            'owner_fname' => $request->get('reg_fname'),
            'owner_lname' => $request->get('reg_lname'),
            'name' => $request->get('reg_restaurant_name'),
            'contact_number' => $request->get('reg_contact_number'),
            'address' => $request->get('reg_address'),
            'slug' => $restaurant_slug
        );
 
        $restaurant = $this->restaurant()->create($restaurant_details);

        foreach (['reg_permit_1', 'reg_permit_2', 'reg_permit_3'] as $key) {
            if ($request->has($key)) {
                $image = $request->file($key);
                $path = $image->getRealPath().'.jpg';
                $file_name = time().rand(1000, 9999).'.jpg';

                $whole_pic = Image::make($image)->encode('jpg')->save($path);
                Storage::putFileAs('permit', new File($path), $file_name);

                $medium = Image::make($image)->resize(300,200)->encode('jpg')->save($path);
                Storage::putFileAs('permit/medium', new File($path), $file_name);

                $thumbnail = Image::make($image)->resize(100, 100)->encode('jpg')->save($path);
                Storage::putFileAs('permit/thumbnail', new File($path), $file_name);

                $permit = $this->permit()->create(['image_name' => $file_name]);
                $this->restaurant()->find($restaurant->id)->permit()->save($permit);
            }
        }

        $user->restaurant()->save($restaurant);

        //$user->notify(new RestaurantPartnership());

        $temp = $this->user()->where('email', $request->get('reg_email'))->get()->first();

        if ($request->header()['origin'][0] == "app") {
            $this->logs()->create([
                "user_id" => $temp['id'],
                "ip_address" => $request->ip(),
                "type" => "Register",
                "description" => "Registered an account",
                "origin" => $request->header()['origin'][0]
            ]);
        }
        
        return response()->json([
            "success" => true,
            "data" => [
                "user_id" => $temp['id']
            ],
            "message" => "Your request has been sent to the administrator, Please wait for our email for further instructions. Thank you!"
        ]);
    }

    public function info(Request $request)
    {
        $rules = [
            'flat_rate' => 'required|digits_between:1,3',
            'eta' => 'required|digits_between:1,3',
            'image_name' => 'required|image|mimes:jpeg,png,jpg|max:5120'
        ];

        if (!$request->has('24hours')) {
            $rules['open_time'] = 'required';
            $rules['close_time'] = 'required';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => "Invalid Input",
                "errors" => $validator->errors()
            ]);
        }

        $vals = [
            "flat_rate" => $request->get('flat_rate'),
            "eta" => $request->get('eta'),
            "open_time" => "00:00:00",
            "close_time" => "00:00:00"
        ];

        if (!$request->has('24hours')) {
            $open_time = Carbon::createFromFormat('H:i a', $request->get('open_time'));
            $close_time = Carbon::createFromFormat('H:i a', $request->get('close_time'));

            if ($open_time->gte($close_time)) {
                return response()->json([
                    "success" => false,
                    "message" => "Invalid Input",
                    "errors" => [
                        "Opening/Closing Time" => [
                            "Please enter a valid opening/closing time."
                        ]
                    ]
                ]);
            }
            $vals['open_time'] = $open_time->format('h:m:s');
            $vals['close_time'] = $close_time->format('h:m:s');
        }

        $restaurant_id = auth()->user()->restaurant()->value('id');

        $image = $request->file('image_name');
        $path = $image->getRealPath().'.jpg';
        $file_name = time().rand(1000, 9999).'.jpg';

        $whole_pic = Image::make($image)->encode('jpg')->save($path);
        Storage::putFileAs('restaurant', new File($path), $file_name);

        $medium = Image::make($image)->resize(300,200)->encode('jpg')->save($path);
        Storage::putFileAs('restaurant/medium', new File($path), $file_name);

        $thumbnail = Image::make($image)->resize(100, 100)->encode('jpg')->save($path);
        Storage::putFileAs('restaurant/thumbnail', new File($path), $file_name);

        $vals['image_name'] = $file_name;

        $this->restaurant()->find($restaurant_id)->update($vals);

        if ($request->header()['origin'][0] == "app") {
            $this->logs()->create([
                "user_id" => auth()->user()->id,
                "ip_address" => $request->ip(),
                "type" => "Update",
                "description" => "Restaurant Profile",
                "origin" => $request->header()['origin'][0]
            ]);
        }

        return response()->json([
            "success" => true,
            "message" => "Successfully updated restaurant information",
            "data" => [
                "image_name" => $file_name
            ]
        ]);
    }

    public function restaurant_order_view($restaurant_id, $sub_order_id, Request $request)
    {
        $order_check = $this->order()->orderCheck($sub_order_id, $restaurant_id);

        if (!$order_check) {
            return response()->json([
                "success" => false,
                "message" => "Order doesn't exist!"
            ]);
        }

        $orders = $this->suborder()->where('id', $sub_order_id)->with(['itemlist'])->get()->first();
        
        $order = $this->suborder()->orderFind($sub_order_id);
        $customer = $this->order()->find($order->id)->customer()->get()->first();
        $user = $this->customer()->find($customer['id'])->user()->get()->first();
        $ban = $this->user()->find($user['id'])->ban()->exists();
        $orders['date_created'] = Carbon::parse($order->created_at)->format('F d, Y h:i A');
        $orders['date_expire'] = Carbon::parse($orders['expires_at'])->format('F d, Y h:i A');
        $orders['order'] = $order;
        $orders['customer'] = $customer;
        $orders['customer']['banned'] = $ban;
                
        $orders['restaurant_id'] = $restaurant_id;

        return response()->json([
            "success" => true,
            "data" => $orders
        ]);
    }
}
