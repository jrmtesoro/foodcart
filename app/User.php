<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Laravel\Passport\HasApiTokens;
use Carbon\Carbon;
use DB;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'password', 'access_level'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    
    protected $table = "user";

    protected $hidden = [
        'password', 'remember_token', 'pivot'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function change_request()
    {
        return $this->belongsToMany('App\ChangeRequest', 'user_change_request');
    }

    public function restaurant() 
    {
        return $this->belongsToMany('App\Restaurant', 'user_restaurant');
    }

    public function customer()
    {
        return $this->belongsToMany('App\Customer', 'user_customer');
    }

    public function password_reset()
    {
        return $this->belongsToMany('App\PasswordReset', 'user_password_reset');
    }

    public function ban()
    {
        return $this->belongsToMany('App\Ban', 'user_ban');
    }

    public function admin()
    {
        return $this->belongsToMany('App\Admin', 'user_admin');
    }

    public function verification()
    {
        return $this->belongsToMany('App\Verification', 'user_verification');
    }

    public function register($details)
    {
        $user = User::create($details);
        return $user;
    }

    public function verifiedUser($user_id)
    {
        $user = User::where('id', $user_id)->get()->first();
        
        if ($user['email_verified_at'] == null) {
            return false;
        }

        return true;
    }

    public function checkResetPasswordToken($token)
    {
        $user = DB::table('password_resets')->select('email')
                ->where('token', '=', bcrypt($token))
                ->where('created_at', '>', Carbon::today())
                ->get()
                ->first(); 

        return $user;
    }
}
