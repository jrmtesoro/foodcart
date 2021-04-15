<?php

namespace App\Http\Controllers\WebController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use GuzzleHttp\Client;
use RealRashid\SweetAlert\Facades\Alert;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $client = new Client();

        $form_params = array(
            "login_email" => $request->get('login_email'),
            "login_password" => $request->get('login_password')
        );

        $req = $client->request("POST", $this->url."login", [
            "headers" => $this->header,
            "form_params" => $form_params,
            'http_errors' => false
        ]);

        $status_code = $req->getStatusCode();

        $result = json_decode($req->getBody()->getContents(), true);

        if($status_code == 429) {
            Alert::error('Login Failed', $result['errors']['email'][0]);
            return redirect()->route('login')->withInput();
        }

        if (!$result['success']) {
            if ($result['message'] == 'Invalid Email/Password') {
                $client1 = new Client();
                $log_store = $client1->request("POST", $this->url."logs1", [
                    "headers" => $this->header,
                    "form_params" => [
                        "ip_address" => $request->ip(),
                        "type" => "Login",
                        "description" => "Attempted to login email : ".$request->get('login_email'),
                        "origin" => "web"
                    ]
                ]);
                Alert::error('Login Failed', $result['message']);
                return redirect()->route('login')->withInput();
            } else if ($result['message'] == 'Your account has been banned') {
                $client1 = new Client();
                $log_store = $client1->request("POST", $this->url."logs1", [
                    "headers" => $this->header,
                    "form_params" => [
                        "ip_address" => $request->ip(),
                        "type" => "Login",
                        "description" => "Attempted to login banned email : ".$request->get('login_email'),
                        "origin" => "web"
                    ]
                ]);
                Alert::error('Login Failed', $result['message']);
                return redirect()->route('login')->withInput();
            }

            $session_values = array(
                "token" => $result['data']['token'],
                "verified" => false
            );

            session($session_values);

            $client1 = new Client();
            $log_store = $client1->request("POST", $this->url."logs1", [
                "headers" => $this->header,
                "form_params" => [
                    "ip_address" => $request->ip(),
                    "type" => "Login",
                    "description" => "Attempted to login unverified email : ".$request->get('login_email'),
                    "origin" => "web"
                ]
            ]);

            return redirect()->route('login')
                    ->with('verification', true)
                    ->withInput($request->all());
        }

        $session_values = array(
            "access_level" => $result['data']['access_level'],
            "token" => $result['data']['token']
        );
        
        $route = "home";

        if ($result['data']['access_level'] == 3) {
            $route = "admin.dashboard";
            $session_values['fname'] = $result['data']['fname'];
            $session_values['lname'] = $result['data']['lname'];
        } else {
            $session_values['fname'] = $result['data']['fname'];
            $session_values['lname'] = $result['data']['lname'];
            if ($result['data']['access_level'] == 2) {
                $session_values['restaurant_id'] = $result['data']['restaurant_id'];
                $session_values['restaurant_name'] = $result['data']['restaurant_name'];
                $session_values['restaurant_image'] = $result['data']['restaurant_image'];
                $session_values['info'] = $result['data']['info'];
                if ($result['data']['info']) {
                    $route = "owner.info";
                } else {
                    $route = "owner.dashboard";
                }
            }
        }

        $client1 = new Client();
        $log_store = $client1->request("POST", $this->url."logs1", [
            "headers" => $this->header,
            "form_params" => [
                "user_id" => $result['data']['user_id'],
                "ip_address" => $request->ip(),
                "type" => "Login",
                "description" => "Logged In",
                "origin" => "web"
            ]
        ]);

        session($session_values);        
        return redirect()->route($route);
    }

    public function update(Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("POST", $this->url."user/update", [
            "headers" => $this->header,
            "form_params" => [
                "user_old_password" => $request->get('user_old_password'),
                "user_password" => $request->get('user_password'),
                "user_password1" => $request->get('user_password1')
            ]
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        if (!$result['success']) {
            Alert::error('Changed Password Failed', $result['message']);
            return redirect()->back()
                    ->with('user_error', $result['errors']);
        }

        $client1 = new Client();
        $log_store = $client1->request("POST", $this->url."logs", [
            "headers" => $this->header,
            "form_params" => [
                "ip_address" => $request->ip(),
                "type" => "Update",
                "description" => "Changed Password",
                "origin" => "web"
            ]
        ]);

        Alert::success('Changed Password Success', $result['message']);
        return redirect()->back();
    }

    public function register(Request $request)
    {
        $form_params = $request->all();
        
        $client = new Client();

        $req = $client->request("POST", $this->url."register/customer", [
            "headers" => $this->header,
            "form_params" => $form_params
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        if (!$result['success']) {
            $msg = $result['errors']['reg_email'][0];
            Alert::error('Registration Failed', $msg);
            return redirect()->back()
                    ->withInput($request->all());
        }

        $client1 = new Client();
        $log_store = $client1->request("POST", $this->url."logs1", [
            "headers" => $this->header,
            "form_params" => [
                "user_id" => $result['data']['user_id'],
                "ip_address" => $request->ip(),
                "type" => "Register",
                "description" => "Registered",
                "origin" => "web"
            ]
        ]);
        
        Alert::success('Registration Successful', $result['message']);
        return redirect()->route('login')
                ->with('code', $result['data']['code'])
                ->with('verification', true);
    }

    public function verify($user_id, Request $request)
    {
        //http://localhost/fcart/public/email/verify/2?expires=1553526850&signature=01458773cc32b31dd5c617fcb9058ac61c84ea9c23b067ba19d0258d8d7e4134

        $query = array(
            "expires" => $request->get('expires'),
            "signature" => $request->get('signature')
        );

        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("GET", $this->url."email/verify/$user_id", [
            "headers" => $this->header,
            "query" => $query
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        if (!$result['success']) {
            return redirect()->route('guest.login')
                ->with('verified', false)
                ->with('message', $result['message']);
        }
        
        $client1 = new Client();
        $log_store = $client1->request("POST", $this->url."logs1", [
            "headers" => $this->header,
            "form_params" => [
                "user_id" => $result['data']['user_id'],
                "ip_address" => $request->ip(),
                "type" => "Verify",
                "description" => "Verified Account",
                "origin" => "web"
            ]
        ]);

        return redirect()->route('guest.login')
                ->with('verified', true)
                ->with('message', $result['message']);
    }

    public function resend(Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $request = $client->request("GET", $this->url."email/resend", [
            "headers" => $this->header
        ]);

        $result = json_decode($request->getBody()->getContents(), true);

        if (!$result['success']) {
            Alert::error('Email Verification Failed', $result['message']);
            return redirect()->back();       
        }

        $client1 = new Client();
        $log_store = $client1->request("POST", $this->url."logs1", [
            "headers" => $this->header,
            "form_params" => [
                "user_id" => $result['data']['user_id'],
                "ip_address" => $request->ip(),
                "type" => "Verify",
                "description" => "Sent a verification email",
                "origin" => "web"
            ]
        ]);

        return redirect()->route('login')
                ->with('message', $result['message'])
                ->with('resent', true);
    }

    public function logout(Request $request)
    {
        $keys = array(
            'fname', 'lname', 'token', 'access_level', 'verified', 'restaurant_id',
            'restaurant_name', 'restaurant_image', 'info'
        );
        if (session()->has('access_level')) {
            $this->header['Authorization'] = 'Bearer '.session()->get('token');
            
            $client1 = new Client();
            $log_store = $client1->request("POST", $this->url."logs", [
                "headers" => $this->header,
                "form_params" => [
                    "ip_address" => $request->ip(),
                    "type" => "Logout",
                    "description" => "Logged Out",
                    "origin" => "web"
                ],
                'http_errors' => false
            ]);

            foreach ($keys as $key) {
                if (session()->has($key)) {
                    session()->forget($key);
                }
            }
            
            $client = new Client();

            $req = $client->request("GET", $this->url."logout", [
                "headers" => $this->header,
                'http_errors' => false
            ]);
    
            $status_code = $req->getStatusCode();
            
            if ($status_code == 401) {
                foreach ($keys as $key) {
                    if (session()->has($key)) {
                        session()->forget($key);
                    }
                }
            }
        }
        foreach ($keys as $key) {
            if (session()->has($key)) {
                session()->forget($key);
            }
        }
        return redirect()->route('home');
    }
}
