<?php

namespace App\Http\Controllers\ApiController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Image;
use Illuminate\Http\File;


class MenuController extends Controller
{
    public function index(Request $request)
    {
        $restaurant_id = auth()->user()->restaurant()->value('id');

        $get = array(
            'menu.name', 'menu.description',
            'menu.price', 'menu.cooking_time', 'menu.image_name',
            'category.name as category_name', 'menu.id',
            'menu.created_at'
        );
        
        $restaurant_menu = $this->menu()->getMenus($get, $restaurant_id, $request->get('search'));

        $response = array(
            "success" => true,
        );

        if ($restaurant_menu->count() == 0) {
            $response['message'] = "No Data Found";
        } else {
            $response['data'] = $restaurant_menu;
        }

        return response()->json($response);
    }

    public function create()
    {
        $restaurant_id = auth()->user()->restaurant()->value('id');

        $tag_list = $this->tag()->getTags(['name'], 1)->pluck('name')->toArray();

        $category_list = $this->category()
                ->getCategory(['category.name', 'category.id'], $restaurant_id, ['category.name', 'asc'])
                ->pluck('name', 'id');
        
        $data = array(
            "tag_list" => $tag_list,
            "category_list" => $category_list
        );

        return response()->json([
            "success" => true,
            "data" => $data
        ]);
    }

    public function store(Request $request)
    {
        $temp_array = [];
        if ($request->has('menu_tag')) {
            $tag_array = explode(',', $request->get('menu_tag'));

            if (count($tag_array) > 5) {
                return response()->json([
                    'success' => false,
                    'message' => "The maximum tags you can enter is : 5"
                ]);
            }

            foreach ($tag_array as $tag) {
                $tag = $this->tag()->where('name', $tag)->first();
                if ($tag) {
                    if ($tag->status == 2) {
                        $temp_array[] = $tag->name;
                    }
                }
            }
        }

        if (count($temp_array) != 0) {
            $rejected = implode(', ', $temp_array);
            return response()->json([
                'success' => false,
                'message' => "The following tags are rejected: $rejected"
            ]);
        }

        $restaurant_id = auth()->user()->restaurant()->value('id');

        $validator = Validator::make($request->all(), [
            'menu_name' => 'required|min:6|max:50',
            'menu_price' => 'required|numeric|digits_between:1,4|min:0|not_in:0',
            'menu_cooking_time' => 'required|numeric|digits_between:1,3|min:0|not_in:0',
            'menu_category' => 'required|exists:category,id',
            'menu_image' => 'image|mimes:jpeg,png,jpg|max:5120'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => "Invalid Input",
                "errors" => $validator->errors()
            ]);
        }

        $menus = $this->restaurant()->find($restaurant_id)->menu()->get();

        foreach ($menus as $m) {
            if (strtolower($request->get('menu_name')) == strtolower($m->name)) {
                return response()->json([
                    "success" => false,
                    "message" => "Duplicate item name found!",
                ]);
            }
        }

        $values = array(
            'name' => $request->get('menu_name'),
            'description' => $request->get('menu_description'),
            'price' => $request->get('menu_price'),
            'cooking_time' => $request->get('menu_cooking_time')
        );

        if (!empty($request->file('menu_image'))) {
            $image = $request->file('menu_image');
            $path = $image->getRealPath().'.jpg';
            $file_name = time().rand(1000, 9999).'.jpg';

            $whole_pic = Image::make($image)->encode('jpg')->save($path);
            Storage::putFileAs('menu', new File($path), $file_name);

            $medium = Image::make($image)->resize(300,200)->encode('jpg')->save($path);
            Storage::putFileAs('menu/medium', new File($path), $file_name);

            $thumbnail = Image::make($image)->resize(100, 100)->encode('jpg')->save($path);
            Storage::putFileAs('menu/thumbnail', new File($path), $file_name);

            $values['image_name'] = $file_name;
        }

        $menu_slug = str_slug($values['name'], '-');
        $all_slugs = $this->menu()->getRelatedSlugs($menu_slug);

        if ($all_slugs->contains('slug', $menu_slug)) {
            for ($i = 0; $i <= 100; $i++) {
                $temp = $menu_slug.'-'.$i;
                if (!$all_slugs->contains('slug', $temp)) {
                    $menu_slug = $temp;
                    break;
                }
            }
        }

        $values['slug'] = $menu_slug;
        $menu = $this->menu()->insertMenu($values);
        $this->restaurant()->find($restaurant_id)->menu()->save($menu);
        $category = $this->category()->find($request->get('menu_category'));
        $this->menu()->find($menu->id)->category()->save($category);

        if (!empty($request->menu_tag)) {
            $tag_array = explode(',', $request->get('menu_tag'));

            foreach ($tag_array as $tag) {
                $tag_check = $this->tag()->where('slug', str_slug($tag))->get()->first();
                if ($tag_check) {   
                    $this->menu()->find($menu->id)->tag()->attach($tag_check['id']);
                } else {
                    $this->menu()->find($menu->id)->tag()->create([
                        "name" => strtolower($tag),
                        "slug" => str_slug($tag)
                    ]);
                }
            }
        }

        if ($request->header()['origin'][0] == "app") {
            $this->logs()->create([
                "user_id" => auth()->user()->id,
                "ip_address" => $request->ip(),
                "type" => "Insert",
                "description" => "Menu name : ".$menu->name,
                "origin" => $request->header()['origin'][0]
            ]);
        }

        return response()->json([
            "success" => true,
            "message" => "Successfully added the item!"
        ]);
    }

    public function edit($menu)
    {
        $restaurant_id = auth()->user()->restaurant()->value('id');

        $menu_details = $this->menu()->getMenu($menu, $restaurant_id);

        if ($menu_details->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No Data Found'
            ]);
        }

        $menu_details = $menu_details->first()->toArray();

        // $temp_array = [];
        // foreach ($menu_details['tag'] as $tag) {
        //     $temp_array[] = $tag['name'];
        // }

        // $menu_details['tag'] = $temp_array;

        $tag_list = $this->tag()->getTags(['name'], 1)->pluck('name')->toArray();

        $category_list = $this->category()
                        ->getCategory(['category.name', 'category.id'], $restaurant_id, ['category.name', 'asc'])
                        ->pluck('name', 'id');

        $data = array(
            "tag_list" => $tag_list,
            "category_list" => $category_list,
            "menu_details" => $menu_details
        );

        return response()->json([
            "success" => true,
            "data" => $data
        ]);
    }

    public function show($menu, Request $request)
    {
        $restaurant_id = auth()->user()->restaurant()->value('id');

        $menu_details = $this->menu()->getMenu($menu, $restaurant_id);

        if ($menu_details->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No Data Found'
            ]);
        }

        $menu_details = $menu_details->first()->toArray();

        // $temp_array = [];
        // foreach ($menu_details['tag'] as $tag) {
        //     $temp_array[] = $tag['name'];
        // }

        // $menu_details['tag'] = $temp_array;

        return response()->json([
            "success" => true,
            "data" => $menu_details
        ]);
    }

    public function update($menu, Request $request)
    {
        $temp_array = [];
        if ($request->has('menu_tag')) {
            $tag_array = explode(',', $request->get('menu_tag'));

            if (count($tag_array) > 5) {
                return response()->json([
                    'success' => false,
                    'message' => "The maximum tags you can enter is : 5"
                ]);
            }

            foreach ($tag_array as $tag) {
                $tag = $this->tag()->where('name', $tag)->first();
                if ($tag) {
                    if ($tag->status == 2) {
                        $temp_array[] = $tag->name;
                    }
                }
            }
        }

        if (count($temp_array) != 0) {
            $rejected = implode(', ', $temp_array);
            return response()->json([
                'success' => false,
                'message' => "The following tags are rejected: $rejected"
            ]);
        }

        $restaurant_id = auth()->user()->restaurant()->value('id');
        $exists = $this->restaurant()->find($restaurant_id)->menu()->where('menu.id', $menu)->exists();
        if (!$exists) {
            return response()->json([
                "success" => false,
                "message" => "Menu doesn't exist!"
            ]);
        }

        $validator = Validator::make($request->all(), [
            'menu_name' => 'required|min:6|max:50',
            'menu_price' => 'required|numeric|digits_between:1,4|min:0|not_in:0',
            'menu_cooking_time' => 'required|numeric|digits_between:1,3|min:0|not_in:0',
            'menu_category' => 'required|exists:category,id',
            'menu_image' => 'image|mimes:jpeg,png,jpg|max:5120'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => "Invalid Input",
                "errors" => $validator->errors()
            ]);
        }

        $menus = $this->restaurant()->find($restaurant_id)->menu()->get();
        $default_menu = $this->menu()->find($menu)->get()->first();

        foreach ($menus as $m) {
            if (strtolower($request->get('menu_name')) == strtolower($m->name) && strtolower($default_menu['name']) != strtolower($request->get('menu_name'))) {
                return response()->json([
                    "success" => false,
                    "message" => "Duplicate item name found!",
                ]);
            }
        }

        $values = array(
            'name' => $request->get('menu_name'),
            'description' => $request->get('menu_description'),
            'price' => $request->get('menu_price'),
            'cooking_time' => $request->get('menu_cooking_time'),
            'slug' => str_slug($request->get('menu_name'))
        );
        
        if ($request->hasFile('menu_image')) {
            $rest = $this->menu()->where('menu.id', $menu)->get()->first()->toArray();
            
            if (!empty($rest['image_name'])) {
                $file_name = $rest['image_name'];
                Storage::delete('menu/'.$file_name);
                Storage::delete('menu/medium/'.$file_name);
                Storage::delete('menu/thumbnail/'.$file_name);
            }

            $image = $request->file('menu_image');
            $path = $image->getRealPath().'.jpg';
            $file_name = time().rand(1000, 9999).'.jpg';

            $whole_pic = Image::make($image)->encode('jpg')->save($path);
            Storage::putFileAs('menu', new File($path), $file_name);

            $medium = Image::make($image)->resize(300,200)->encode('jpg')->save($path);
            Storage::putFileAs('menu/medium', new File($path), $file_name);

            $thumbnail = Image::make($image)->resize(100, 100)->encode('jpg')->save($path);
            Storage::putFileAs('menu/thumbnail', new File($path), $file_name);

            $values['image_name'] = $file_name;
        }

        $this->menu()->find($menu)->update($values);
        $this->menu()->find($menu)->category()->sync([$request->get('menu_category')]);

        $tag_ids = [];
        if (!empty($request->menu_tag)) {
            $tag_array = explode(',', $request->get('menu_tag'));
            foreach ($tag_array as $tag) {
                $tag = $this->tag()->firstOrCreate(['slug' => str_slug($tag)]);
                $tag_ids[] = $tag->id;
            }
        }

        $this->menu()->find($menu)->tag()->sync($tag_ids);

        if ($request->header()['origin'][0] == "app") {
            $this->logs()->create([
                "user_id" => auth()->user()->id,
                "ip_address" => $request->ip(),
                "type" => "Update",
                "description" => "Menu name : ".$request->get('menu_name'),
                "origin" => $request->header()['origin'][0]
            ]);
        }

        return response()->json([
            "success" => true,
            "message" => "Successfully updated the item!"
        ]);
    }

    public function destroy($menu, Request $request)
    {
        $restaurant_id = auth()->user()->restaurant()->value('id');
        $exists = $this->restaurant()->find($restaurant_id)->menu()->where('menu.id', $menu)->exists();
        if (!$exists) {
            return response()->json([
                "success" => false,
                "message" => "Menu doesn't exist!"
            ]);
        }

        $menu_details = $this->menu()->find($menu)->get()->first();

        if ($request->header()['origin'][0] == "app") {
            $this->logs()->create([
                "user_id" => auth()->user()->id,
                "ip_address" => $request->ip(),
                "type" => "Delete",
                "description" => "Menu name : ".$menu_details['name'],
                "origin" => $request->header()['origin'][0]
            ]);
        }

        $this->menu()->find($menu)->delete();

        return response()->json([
            "success" => true,
            "message" => "Successfully hidden the item!"
        ]);
    }

    public function restore($menu, Request $request)
    {
        $restaurant_id = auth()->user()->restaurant()->value('id');
        $exists = $this->restaurant()->find($restaurant_id)->menu()->withTrashed()->where('menu.id', $menu)->exists();
        if (!$exists) {
            return response()->json([
                "success" => false,
                "message" => "Menu doesn't exist!"
            ]);
        }

        $menu_details = $this->menu()->withTrashed()->find($menu)->get()->first();

        if ($request->header()['origin'][0] == "app") {
            $this->logs()->create([
                "user_id" => auth()->user()->id,
                "type" => "Update",
                "description" => "Menu name : ".$menu_details['name']." restored",
                "origin" => $request->header()['origin'][0]
            ]);
        }

        $this->menu()->withTrashed()->find($menu)->restore();

        return response()->json([
            "success" => true,
            "message" => "Successfully shown the item!"
        ]);
    }
}
