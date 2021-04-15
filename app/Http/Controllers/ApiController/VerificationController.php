<?php

namespace App\Http\Controllers\ApiController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notifications\EmailVerification;
use Carbon\Carbon;

class VerificationController extends Controller
{
    public function verify(Request $request)
    {
        if (!$request->has('verification_token')) {
            return response()->json([
                "success" => false,
                "message" => "The token is expired or invalid!"
            ]);
        }

        $verif = $this->verification()->where('token', $request->get('verification_token'))->get()->first();

        if (!$verif) {
            return response()->json([
                "success" => false,
                "message" => "The token is expired or invalid!"
            ]);
        }

        $user_info = $this->verification()->find($verif['id'])->user()->get()->first();
        
        $isVerified = $this->user()->verifiedUser($user_info['id']);

        if ($isVerified) {
            return response()->json([
                "success" => false,
                "message" => "Your account is already verified!"
            ]);
        }
        
        if ($verif['token'] != $request->get('verification_token') || Carbon::parse($verif['created_at'])->addMinutes(30)->isPast()) {
            return response()->json([
                "success" => false,
                "message" => "The token is expired or invalid!"
            ]);
        }

        $this->user()->find($user_info['id'])->markEmailAsVerified();
        $user = $this->user()->find($user_info['id'])->get()->first();

        if ($request->header()['origin'][0] == "app") {
            $this->logs()->create([
                "user_id" => $user_info['id'],
                "ip_address" => $request->ip(),
                "type" => "Verify",
                "description" => "Verified Account",
                "origin" => $request->header()['origin'][0]
            ]);
        }

        return response()->json([
            "success" => true,
            "message" => "You have successfully verified your account!",
            "data" => [
                "access_level" => $user['access_level'],
                "user_id" => $user_info['id']
            ]
        ]);
    }
    
    public function resend(Request $request)
    {
        $user_id = auth()->user()->id;

        $verification = $this->user()->find($user_id)->verification()->get()->first();

        $code = str_random(6);
        if ($verification) {
            $this->verification()->find($verification->id)->user()->detach($user_id);
            $this->verification()->find($verification->id)->delete();
        }

        $verif = $this->verification()->create([
            "token" => $code
        ]);
        $this->user()->find($user_id)->verification()->save($verif);

        $user = $this->user()->where('id', $user_id)->get()->first();

        //$this->user()->find($user_id)->notify(new EmailVerification($code));

        if ($request->header()['origin'][0] == "app") {
            $this->logs()->create([
                "user_id" => $user_id,
                "ip_address" => $request->ip(),
                "type" => "Verify",
                "description" => "Sent a verification email",
                "origin" => $request->header()['origin'][0]
            ]);
        }

        return response()->json([
            "success" => true,
            "data" => [
                "user_id" => $user_id,
                "code" => $code
            ],
            "message" => "Email verification has been sent to ".$user['email']
        ]);
    }
}
