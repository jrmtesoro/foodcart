<?php

namespace App\Http\Controllers\ApiController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class TagController extends Controller
{
    public function index(Request $request)
    {
        $tag = \DB::table('tag')->select(['tag.id', 'tag.name', 'tag.slug', 'tag.status', 'tag.created_at', \DB::raw('COUNT(menu.id) as used_by')])
                ->leftJoin('menu_tag', function ($query) {
                    $query->on('menu_tag.tag_id', '=', 'tag.id');
                })
                ->leftJoin('menu', function ($query) {
                    $query->on('menu.id', '=', 'menu_tag.menu_id');
                })
                ->groupBy('tag.slug');

        if ($request->has('filter')) {
            if ($request->get('filter') !== null) {
                $filter = $request->get('filter');
                $status = 0;
                if ($filter == 'rejected') {
                    $status = 2;
                } else if ($filter == 'accepted') {
                    $status = 1;
                } else if ($filter == 'pending') {
                    $status = 0;
                }
            }
            $tag->where('status', $status); 
        }

        return response()->json([
            "success" => true,
            "data" => $tag->get()
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tag_name' => 'required|string|min:5|max:21'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                'message' => 'Invalid Input',
                'errors' => $validator->errors()
            ]);
        }

        $tag_name = strtolower($request->get('tag_name'));

        $tag = $this->tag()->where('name', $tag_name)->first();
        if ($tag) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Input',
                'errors' => [
                    'tag_name' => ['Duplicate found! Please enter another tag name']
                ]
            ]);
        }

        $values = array(
            'name' => $tag_name,
            'slug' => str_slug($tag_name),
            'status' => 1
        );
        
        $this->tag()->create($values);

        if ($request->header()['origin'][0] == "app") {
            $this->logs()->create([
                "user_id" => auth()->user()->id,
                "ip_address" => $request->ip(),
                "type" => "Insert",
                "description" => "Tag name : ".$request->get('tag_name'),
                "origin" => $request->header()['origin'][0]
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Successfully added tag!'
        ]);
    }

    public function reject($tag, Request $request)
    {
        $req = array(
            'tag_id' => $tag
        );

        $validator = Validator::make($req, [
            'tag_id' => 'required|exists:tag,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Input',
                'errors' => $validator->errors()
            ]);
        }

        $this->tag()->find($tag)->update(['status' => 2]);

        $tag = $this->tag()->find($tag)->get()->first();

        if ($request->header()['origin'][0] == "app") {
            $this->logs()->create([
                "user_id" => auth()->user()->id,
                "ip_address" => $request->ip(),
                "type" => "Update",
                "description" => "Tag name : ".$tag['name']." rejected",
                "origin" => $request->header()['origin'][0]
            ]);
        }

        return response()->json([
            'success' => true,
            "data" => [
                "tag_name" => $tag['name']
            ],
            'message' => 'Tag has been rejected'
        ]);
    }

    public function accept($tag, Request $request)
    {
        $req = array(
            'tag_id' => $tag
        );

        $validator = Validator::make($req, [
            'tag_id' => 'required|exists:tag,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Input',
                'errors' => $validator->errors()
            ]);
        }

        $this->tag()->find($tag)->update(['status' => 1]);

        $tag = $this->tag()->find($tag)->get()->first();

        if ($request->header()['origin'][0] == "app") {
            $this->logs()->create([
                "user_id" => auth()->user()->id,
                "ip_address" => $request->ip(),
                "type" => "Update",
                "description" => "Tag name : ".$tag['name']." accepted",
                "origin" => $request->header()['origin'][0]
            ]);
        }


        return response()->json([
            'success' => true,
            "data" => [
                "tag_name" => $tag['name']
            ],
            'message' => 'Tag has been accepted'
        ]);
    }
}
