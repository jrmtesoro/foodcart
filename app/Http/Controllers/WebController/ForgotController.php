<?php

namespace App\Http\Controllers\WebController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use RealRashid\SweetAlert\Facades\Alert;

class ForgotController extends Controller
{
    public function forgotForm()
    {
        return view('guest.pages.forgot');
    }

    public function forgot(Request $request)
    {
        $form_params = array(
            "forgot_email" => $request->get('forgot_email')
        );

        $client = new Client();

        $req = $client->request("POST", $this->url."password/create", [
            "headers" => $this->header,
            "form_params" => $form_params
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        if (!$result['success']) {
            $client1 = new Client();
            $log_store = $client1->request("POST", $this->url."logs1", [
                "headers" => $this->header,
                "form_params" => [
                    "ip_address" => $request->ip(),
                    "type" => "Forgot",
                    "description" => "Attempted to reset password of email : ".$request->get('forgot_email'),
                    "origin" => "web"
                ]
            ]);

            Alert::error('Forgot Password Failed', $result['message']);
            return redirect()->back()->withInput($request->all());
        }

        $client1 = new Client();
        $log_store = $client1->request("POST", $this->url."logs1", [
            "headers" => $this->header,
            "form_params" => [
                "user_id" => $result['data']['user_id'],
                "ip_address" => $request->ip(),
                "type" => "Forgot",
                "description" => "Sent a mail for reset password",
                "origin" => "web"
            ]
        ]);

        Alert::success('Forgot Password Success', $result['message']);
        return redirect()->route('login')->with('forgot_code', $result['data']['forgot_code']);
    }

    public function find($token) 
    {
        if (empty($token)) {
            Alert::error('Password Reset Failed', 'This password reset token is invalid.');
            return redirect()->route('login');
        }

        $client = new Client();

        $req = $client->request("GET", $this->url."password/find/".$token, [
            "headers" => $this->header
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        if (!$result['success']) {
            Alert::error('Password Reset Failed', $result['message']);
            return redirect()->route('login');
        }

        return view('guest.pages.reset')
                ->with('email', $result['data']['email'])
                ->with('token', $token);
    }

    public function reset(Request $request)
    {
        $form_params = array(
            "forgot_token" => $request->get('forgot_token'),
            "forgot_email" => $request->get('forgot_email'),
            "forgot_password" => $request->get('forgot_password'),
            "forgot_password_confirm" => $request->get('forgot_password')
        );

        $client = new Client();

        $req = $client->request("POST", $this->url."password/reset", [
            "headers" => $this->header,
            "form_params" => $form_params
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        if (!$result['success']) {
            Alert::error('Password Reset Failed', $result['message']);
            if (!empty($result['errors'])) {
                return redirect()->back();
            } else {
                return redirect()->route('login');
            }
        }

        $client1 = new Client();
        $log_store = $client1->request("POST", $this->url."logs1", [
            "headers" => $this->header,
            "form_params" => [
                "user_id" => $result['data']['user_id'],
                "ip_address" => $request->ip(),
                "type" => "Forgot",
                "description" => "Reset password completed",
                "origin" => "web"
            ]
        ]);

        Alert::success('Reset Password Success', $result['message']);
        return redirect()->route('login');
    }
}
