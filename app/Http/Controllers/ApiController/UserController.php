<?php

namespace App\Http\Controllers\ApiController;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Notifications\EmailVerification;

class UserController extends Controller
{
    use AuthenticatesUsers;

    public function login(Request $request) 
    {
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        $credentials = [
            'email' => $request->get('login_email'),
            'password' => $request->get('login_password')
        ];
 
        if (!auth()->attempt($credentials)) {
            $this->incrementLoginAttempts($request);
            if ($request->header()['origin'][0] == "app") {
                $this->logs()->create([
                    "ip_address" => $request->ip(),
                    "type" => "Login",
                    "description" => "Attempted to login email : ".$request->get('login_email'),
                    "origin" => $request->header()['origin'][0]
                ]);
            }

            return response()->json([
                "success" => false,
                "message" => "Invalid Email/Password"
            ]);
        }

        $users = auth()->user()->toArray();
        
        $ban = $this->user()->find($users['id'])->ban()->exists();

        if ($ban) {
            if ($request->header()['origin'][0] == "app") {
                $this->logs()->create([
                    "ip_address" => $request->ip(),
                    "type" => "Login",
                    "description" => "Attempted to login banned email : ".$request->get('login_email'),
                    "origin" => $request->header()['origin'][0]
                ]);
            }

            return response()->json([
                "success" => false,
                "message" => "Your account has been banned"
            ]);
        }

        $this->clearLoginAttempts($request);

        $token = auth()->user()->createToken('Access token for '.auth()->user()->value('id').' User ID')->accessToken;

        $user = auth()->user()->toArray();

        $is_verified = $this->user()->verifiedUser($user['id']);

        if (!$is_verified) {
            if ($request->header()['origin'][0] == "app") {
                $this->logs()->create([
                    "user_id" => auth()->user()->id,
                    "ip_address" => $request->ip(),
                    "type" => "Login",
                    "description" => "Attempted to login unverified email : ".$request->get('login_email'),
                    "origin" => $request->header()['origin'][0]
                ]);
            }

            return response()->json([
                "success" => false,
                "message" => "Please verify your email.",
                "data" => array(
                    "token" => $token
                )
            ]);
        }

        $response = array(
            "success" => true,
            "data" => [
                "token" => $token,
                "access_level" => $user['access_level'],
                "user_id" => $user['id']
            ],
            "message" => "Successfully Logged In!"
        );

        $access_level = $user['access_level'];
        
        if ($access_level == 3) {
            $admin_details = auth()->user()->admin();
            $response['data']['fname'] = $admin_details->value('fname');
            $response['data']['lname'] = $admin_details->value('lname');
        } else if ($access_level == 2) {
            $restaurant_details = auth()->user()->restaurant();
            if ($restaurant_details->value('status') != 1) {
                return response()->json([
                    "success" => false,
                    "message" => "Invalid Email/Password"
                ]);
            }
            
            $response['data']['restaurant_id'] = $restaurant_details->value('id');
            $response['data']['fname'] = $restaurant_details->value('owner_fname');
            $response['data']['lname'] = $restaurant_details->value('owner_lname');
            $response['data']['restaurant_name'] = $restaurant_details->value('name');
            $response['data']['restaurant_image'] = $restaurant_details->value('image_name');


            if (empty($restaurant_details->value('flat_rate')) || empty($restaurant_details->value('image_name')) ||
            empty($restaurant_details->value('open_time')) || empty($restaurant_details->value('close_time')) ||
            empty($restaurant_details->value('eta'))) {
                $response['data']['info'] = true;
            } else {
                $response['data']['info'] = false;
            }

        } else if ($access_level == 1) {
            $customer_details = auth()->user()->customer();
            $response['data']['fname'] = $customer_details->value('fname');
            $response['data']['lname'] = $customer_details->value('lname');
        }

        if ($request->header()['origin'][0] == "app") {
            $this->logs()->create([
                "user_id" => auth()->user()->id,
                "ip_address" => $request->ip(),
                "type" => "Login",
                "description" => "Logged In",
                "origin" => $request->header()['origin'][0]
            ]);
        }

        return response()->json($response);
    }

    public function registerCustomer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reg_fname' => 'required|min:3|max:30',
            'reg_lname' => 'required|min:3|max:30',
            'reg_contact_number' => 'required|min:7|max:11',
            'reg_address' => 'required',
            'reg_email' => 'required|unique:user,email',
            'reg_password' => 'required|min:8|max:21',
            'reg_password_confirm' => 'required|min:8|max:21|same:reg_password'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => "Invalid Input",
                "errors" => $validator->errors()
            ]);
        };

        $user_details = array(
            'email' => $request->get('reg_email'),
            'password' => bcrypt($request->get('reg_password'))
        );

        $user = $this->user()->register($user_details);

        $customer_details = array(
            'fname' => $request->get('reg_fname'),
            'lname' => $request->get('reg_lname'),
            'contact_number' => $request->get('reg_contact_number'),
            'address' => $request->get('reg_address')
        );

        $customer = $this->customer()->register($customer_details);
        $user->customer()->save($customer);

        $user_info = $this->user()->where('email', $request->get('reg_email'))->get()->first();

        $code = str_random(6);
        $verif = $this->verification()->create([
            "token" => $code
        ]);
        $this->user()->find($user_info['id'])->verification()->save($verif);
        
        //$this->user()->find($user_info['id'])->notify(new EmailVerification($code));

        $token = $user->createToken('Access token for '.$user->id.' User ID')->accessToken;

        if ($request->header()['origin'][0] == "app") {
            $this->logs()->create([
                "user_id" => $user->id,
                "ip_address" => $request->ip(),
                "type" => "Register",
                "description" => "Registered",
                "origin" => $request->header()['origin'][0]
            ]);
        }
        
        return response()->json([
            "success" => true,
            "data" => array(
                "token" => $token,
                "user_id" => $user->id,
                "code" => $code
            ),
            "message" => "Successfully Registered! Please check your email for the validation link."
        ]);
    }    

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'user_old_password' => 'required',
            'user_password' => 'required|min:8|max:21',
            'user_password1' => 'required|min:8|max:21|same:user_password'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => "Invalid Input",
                "errors" => $validator->errors()
            ]);
        }

        $old_password = auth()->user()->password;
        if (!\Hash::check($request->get('user_old_password'), $old_password)) {
            return response()->json([
                "success" => false,
                "message" => "Invalid Input",
                "errors" => [
                    "user_old_password" => [
                        "Incorrect old password"
                    ]
                ]
            ]);
        }

        if (\Hash::check($request->get('user_password'), $old_password)) {
            return response()->json([
                "success" => false,
                "message" => "Invalid Input",
                "errors" => [
                    "user_old_password" => [
                        "Old and New password must not be the same!"
                    ]
                ]
            ]);
        }

        $user_id = \Auth::user()->id;

        $this->user()->find($user_id)->update([
            "password" => bcrypt($request->get('user_password'))
        ]);

        if ($request->header()['origin'][0] == "app") {
            $this->logs()->create([
                "user_id" => auth()->user()->id,
                "ip_address" => $request->ip(),
                "type" => "Update",
                "description" => "Changed Password",
                "origin" => $request->header()['origin'][0]
            ]);
        }

        return response()->json([
            "success" => true,
            "message" => "Change Password Successful!"
        ]);
    }

    public function logout(Request $request)
    {
        if ($request->header()['origin'][0] == "app") {
            $this->logs()->create([
                "user_id" => auth()->user()->id,
                "ip_address" => $request->ip(),
                "type" => "Logout",
                "description" => "Logged Out",
                "origin" => $request->header()['origin'][0]
            ]);
        }

        $request->user()->token()->revoke();

        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out'
        ]);
    }
}
