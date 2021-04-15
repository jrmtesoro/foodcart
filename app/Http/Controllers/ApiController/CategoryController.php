<?php

namespace App\Http\Controllers\ApiController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

use DB;

class CategoryController extends Controller
{
    public function index()
    {
        $restaurant_id = auth()->user()->restaurant()->value('id');
        $category_list = $this->category()
                ->getCategory(['category.name', 'category.id'], $restaurant_id, ['category.name', 'desc'])
                ->pluck('name', 'id');
        return response()->json([
            'success' => true,
            'data' => $category_list
        ]);
    }

    public function deletedCategory()
    {
        $restaurant_id = auth()->user()->restaurant()->value('id');
        $category_list = $this->category()->deletedCategory($restaurant_id);

        return response()->json([
            'success' => true,
            'data' => $category_list
        ]);
    }

    public function getCategory(Request $request)
    {
        $restaurant_id = auth()->user()->restaurant()->value('id');

        $category_list = DB::table('category')->selectRaw('MIN(category.created_at) as min_c, MAX(category.created_at) max_c, MAX(category.deleted_at) max_d')
            ->join('restaurant_category', 'restaurant_category.category_id', '=', 'category.id')
            ->join('restaurant', 'restaurant.id', '=', 'restaurant_category.restaurant_id')
            ->where('restaurant.id', $restaurant_id)
            ->get()->first();

        return response()->json([
            "success" => true,
            "data" => $category_list
        ]);
    }

    public function update($category, Request $request)
    {
        $restaurant_id = auth()->user()->restaurant()->value('id');
        $exists = $this->restaurant()->find($restaurant_id)->category()->where('category.id', $category)->exists();
        if (!$exists) {
            return response()->json([
                "success" => false,
                "message" => "Category doesn't exist!"
            ]);
        }

        $category_name = $request->get('category_name');
        $filtered_name = strtolower($category_name);
        $filtered_name = str_replace(' ', '', $filtered_name);
        $exist = $this->restaurant()->find($restaurant_id)->category()->withTrashed()
            ->where('category.id', '<>', $category)
            ->whereRaw("LOWER(REPLACE(category.name, ' ', '')) = '".$filtered_name."'")->exists();

        if ($exist) {
            return response()->json([
                "success" => false,
                "message" => "Invalid Input",
                "errors" => [
                    'category_name' => [
                        0 => "Duplicate found, please enter another category name."
                    ]
                ]
            ]);
        }    
        
        $temp['category_name'] = $category_name;

        $validator = Validator::make($temp, [
            "category_name" => "required|min:3|max:20"
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => "Invalid Input",
                "errors" => $validator->errors()
            ]);
        }

        if ($request->header()['origin'][0] == "app") {
            $this->logs()->create([
                "user_id" => auth()->user()->id,
                "ip_address" => $request->ip(),
                "type" => "Update",
                "description" => "Category name : ".$category_name,
                "origin" => $request->header()['origin'][0]
            ]);
        }

        $this->category()->find($category)->update(['name' => $category_name]);

        return response()->json([
            "success" => true,
            "message" => "Successfully edited category!"
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make(['category_name' => $request->get('category_name')], [
            "category_name" => "required|min:3|max:20"
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => "Invalid Input",
                "errors" => $validator->errors()
            ]);
        }

        $category_name = $request->get('category_name');

        $restaurant_id = auth()->user()->restaurant()->value('id');

        $filtered_name = strtolower($category_name);
        $filtered_name = str_replace(' ', '', $filtered_name);
        $exist = $this->restaurant()->find($restaurant_id)->category()->withTrashed()
            ->whereRaw("LOWER(REPLACE(category.name, ' ', '')) = '".$filtered_name."'")
            ->exists();

        if ($exist) {
            return response()->json([
                "success" => false,
                "message" => "Invalid Input",
                "errors" => [
                    'category_name' => [
                        0 => "Duplicate found, please enter another category name."
                    ]
                ]
            ]);
        }

        $category = $this->category()->create(['name' => $request->get('category_name')]);
        $this->restaurant()->find($restaurant_id)->category()->save($category);

        if ($request->header()['origin'][0] == "app") {
            $this->logs()->create([
                "user_id" => auth()->user()->id,
                "ip_address" => $request->ip(),
                "type" => "Insert",
                "description" => "Category name : ".$request->get('category_name'),
                "origin" => $request->header()['origin'][0]
            ]);
        }

        return response()->json([
            "success" => true,
            "message" => "Successfully added category!",
            "data" => [
                "id" => $category->id,
                "name" => $category->name
            ]
        ]);
    }

    public function destroy($category, Request $request)
    {
        $restaurant_id = auth()->user()->restaurant()->value('id');
        $exists = $this->restaurant()->find($restaurant_id)->category()->where('category.id', $category)->exists();
        if (!$exists) {
            return response()->json([
                "success" => false,
                "message" => "Category doesn't exist!"
            ]);
        }

        $count = $this->restaurant()->find($restaurant_id)->category()
                ->join('menu_category', 'menu_category.category_id', '=', 'category.id')
                ->where('category.id', $category)
                ->count();

        if ($count != 0) {
            return response()->json([
                "success" => false,
                "message" => "This category is being used."
            ]);
        }

        $category_details = $this->category()->find($category)->get()->first();

        $this->category()->find($category)->delete();

        if ($request->header()['origin'][0] == "app") {
            $this->logs()->create([
                "user_id" => auth()->user()->id,
                "ip_address" => $request->ip(),
                "type" => "Delete",
                "description" => "Category name : ".$category_details['name'],
                "origin" => $request->header()['origin'][0]
            ]);
        }

        return response()->json([
            "success" => true,
            "message" => "Successfully deleted category!"
        ]);
    }

    public function restore($category, Request $request)
    {
        $restaurant_id = auth()->user()->restaurant()->value('id');
        $exists = $this->restaurant()->find($restaurant_id)->category()->withTrashed()->where('category.id', $category)->exists();
        if (!$exists) {
            return response()->json([
                "success" => false,
                "message" => "Category doesn't exist!"
            ]);
        }

        $category_details = $this->category()->withTrashed()->where('id', $category)->get()->first();

        $this->category()->withTrashed()->find($category)->restore();

        if ($request->header()['origin'][0] == "app") {
            $this->logs()->create([
                "user_id" => auth()->user()->id,
                "ip_address" => $request->ip(),
                "type" => "Update",
                "description" => "Restored Category name : ".$category_details['name'],
                "origin" => $request->header()['origin'][0]
            ]);
        }

        return response()->json([
            "success" => true,
            "message" => "Successfully restored category!"
        ]);
    }
}
