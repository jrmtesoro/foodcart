<?php

namespace App\Http\Controllers\ApiController\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails, AuthenticatesUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function forgot(Request $request)
    {
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        $user = $this->user()->where('email', $request->get('forgot_email'));
        $exists = $user->exists();
        if (!$exists) {
            $this->incrementLoginAttempts($request);
            return response()->json([
                "success" => false,
                "message" => "The email address is not registered."
            ]);
        }

        $this->clearLoginAttempts($request);

        $credentials = array(
            "email" => $request->get('forgot_email')
        );


        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $response = $this->broker()->sendResetLink(
            $credentials
        );

        if ($response != Password::RESET_LINK_SENT) {
            return response()->json([
                "success" => false,
                "message" => "Something went wrong, Please try again later."
            ]);
        } 

        return response()->json([
            "success" => true,
            "message" => "A link has been sent to your email. If no email has arrived, check your spam folder."
        ]);
    }

    public function __construct()
    {
        $this->middleware('guest');
    }
}
