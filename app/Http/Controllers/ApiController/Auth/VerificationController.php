<?php

namespace App\Http\Controllers\ApiController\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected $redirectTo = '/';

    public function show()
    {
        //
    }

    public function verify(Request $request)
    {
        $user_id = $request->route('id');
        $user = $this->user()->find($user_id);

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                "success" => false,
                "message" => "User already have verified email!"
            ]);
        }
        
        $user->markEmailAsVerified();

        return response()->json([
            "success" => true,
            "message" => "You have successfully veried your email address! You may now log in"
        ]);
    }

    public function resend()
    {
        $user = auth()->user();
        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'success' => false,
                'message' => 'User already have verified email!'
            ]);
        }

        $user->sendEmailVerificationNotification();

        $email = $user->value('email');

        return response()->json([
            'success' => true,
            'message' => "An email has been sent to $email. Please check your inbox."
        ]);
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }
}
