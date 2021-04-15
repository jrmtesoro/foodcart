<?php

namespace App\Http\Controllers\ApiController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Notifications\ContactUs;
use App\User;

class ContactUsController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "contact_name" => 'required',
            "contact_email" => 'required|email',
            "contact_message" => 'required'
        ]); 

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => "Invalid Input",
                "errors" => $validator->errors()
            ]);
        }

        $name = $request->get('contact_name');
        $email = $request->get('contact_email');
        $message = $request->get('contact_message');

        $user = new User();
        $user->email = 'admin@pinoyfoodcart.com';
        
        //$user->notify(new ContactUs($name, $email, $message));

        if ($request->header()['origin'][0] == "app") {
            $this->logs()->create([
                "ip_address" => $request->ip(),
                "type" => "Insert",
                "description" => "Contact Us",
                "origin" => $request->header()['origin'][0]
            ]);
        }

        return response()->json([
            "success" => true,
            "message" => "Message has been sent to the administrator!"
        ]);
    }
}
