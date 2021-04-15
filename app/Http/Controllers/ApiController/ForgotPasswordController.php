<?php

namespace App\Http\Controllers\ApiController;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Notifications\PasswordResetRequest;
use App\Notifications\PasswordResetSuccess;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{
    public function create(Request $request)
    {
        $validator = Validator::make(
            ['forgot_email' => $request->get('forgot_email')],
            [
                'forgot_email' => 'required|string|email'
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Input',
                'errors' => $validator->errors()
            ]);
        }

        
        $user = $this->user()->where('email', $request->get('forgot_email'))->first();

        if (!$user) {
            if ($request->header()['origin'][0] == "app") {
                $this->logs()->create([
                    "ip_address" => $request->ip(),
                    "type" => "Forgot",
                    "description" => "Attempted to reset password of email : ".$request->get('forgot_email'),
                    "origin" => $request->header()['origin'][0]
                ]);
            }
            return response()->json([
                'success' => false,
                'message' => 'The email address is not registered.'
            ]);
        }

        $user_id = $user->id;
        $verified = $this->user()->find($user_id)->hasVerifiedEmail();

        if (!$verified) {
            if ($request->header()['origin'][0] == "app") {
                $this->logs()->create([
                    "ip_address" => $request->ip(),
                    "type" => "Forgot",
                    "description" => "Attempted to reset password of email : ".$request->get('forgot_email'),
                    "origin" => $request->header()['origin'][0]
                ]);
            }
            return response()->json([
                'success' => false,
                'message' => 'The email address is not yet verified.'
            ]);
        }

        $password_reset = $this->user()->find($user_id)->password_reset()->first();

        $values = [
            'token' => str_random(6)
        ];

        if (!$password_reset) {
            $this->user()->find($user_id)->password_reset()->create($values);
        } else {
            $this->user()->find($user_id)->password_reset()->update($values);
        }

        // $user->notify(
        //     new PasswordResetRequest($values['token'])
        // );

        if ($request->header()['origin'][0] == "app") {
            $this->logs()->create([
                "ip_address" => $request->ip(),
                "user_id" => $user_id,
                "type" => "Forgot",
                "description" => "Sent a mail for reset password",
                "origin" => $request->header()['origin'][0]
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                "user_id" => $user_id,
                "forgot_code" => $values['token']
            ],
            'message' => 'We have e-mailed your password reset link!'
        ]);
    }

    public function find($token, Request $request)
    {
        $password_reset = $this->password_reset()->where('token', '=', $token)->first();
        if (!$password_reset) {
            return response()->json([
                'success' => false,
                'message' => 'This password reset token is invalid.'
            ]);
        }

        if (Carbon::parse($password_reset->created_at)->addMinutes(720)->isPast()) {
            $password_reset->delete();
            return response()->json([
                'success' => false,
                'message' => 'This password reset token is invalid.'
            ]);
        }

        $email = $this->password_reset()->find($password_reset->id)->user()->value('email');

        return response()->json([
                'success' => true,
                'message' => 'Password reset token is valid.',
                'data' => [
                    'email' => $email
                ]
            ]);
    }

    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "forgot_token" => 'required',
            "forgot_email" => 'required|string|email',
            "forgot_password" => 'required|string|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,21}$/',
            "forgot_password_confirm" => 'required|same:forgot_password'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Input',
                'errors' => $validator->errors()
            ]);
        }

        $user_details = $this->user()->where('email', '=', $request->get('forgot_email'))->first();

        if (!$user_details) {
            return response()->json([
                'success' => false,
                'message' => "The email address is not registered!"
            ]);
        }

        $user_id = $user_details->id;

        $password_reset = $this->user()->find($user_id)->password_reset()->where('token', '=', $request->get('forgot_token'))->first();

        if (!$password_reset) {
            return response()->json([
                'success' => false,
                'message' => "This password reset token is invalid."
            ]);
        }

        if (Carbon::parse($password_reset->created_at)->addMinutes(720)->isPast()) {
            $password_reset->delete();
            return response()->json([
                'success' => false,
                'message' => 'This password reset token is invalid.'
            ]);
        }

        $values = array(
            "password" => bcrypt($request->get('forgot_password'))
        );

        $user = $this->user()->find($user_id);

        $user->update($values);
        
        $password_reset =$user->password_reset()->delete();

        //$user->notify(new PasswordResetSuccess($password_reset));
        
        if ($request->header()['origin'][0] == "app") {
            $this->logs()->create([
                "ip_address" => $request->ip(),
                "user_id" => $user_id,
                "type" => "Forgot",
                "description" => "Reset password completed",
                "origin" => $request->header()['origin'][0]
            ]);
        };

        return response()->json([
            'success' => true,
            'data' => [
                "user_id" => $user_id
            ],
            'message' => "You have successfully changed your password!"
        ]);
    }
}
