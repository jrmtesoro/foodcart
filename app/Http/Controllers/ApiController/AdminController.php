<?php

namespace App\Http\Controllers\ApiController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function show(Request $request)
    {
        $admin_id = auth()->user()->admin()->value('id');

        $admin = $this->admin()->find($admin_id)->get()->first();
        $user = $this->admin()->find($admin_id)->user()->get()->first();

        return response()->json([
            "success" => true,
            "data" => [
                "admin" => $admin,
                "user" => $user
            ]
        ]);
    }

    public function update(Request $request)
    {
        $admin_id = auth()->user()->admin()->value('id');

        $validator = Validator::make($request->all(), [
            "fname" => "required|min:3|max:30",
            "lname" => "required|min:3|max:30",
            'address' => 'required',
            'contact_number' => 'required|min:7|max:11'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => "Invalid Input",
                "errors" => $validator->errors()
            ]);
        }

        $this->admin()->find($admin_id)->update([
            "fname" => $request->get('fname'),
            "lname" => $request->get('lname'),
            "address" => $request->get('address'),
            "contact_number" => $request->get('contact_number')
        ]);
        if ($request->header()['origin'][0] == "app") {
            $this->logs()->create([
                "user_id" => auth()->user()->id,
                "ip_address" => $request->ip(),
                "type" => "Update",
                "description" => "Admin Profile",
                "origin" => $request->header()['origin'][0]
            ]);
        }

        return response()->json([
            "success" => true,
            "message" => "Successfully updated profile!",
            "data" => [
                "fname" => $request->get('fname'),
                "lname" => $request->get('lname')
            ]
        ]);
    }
}
