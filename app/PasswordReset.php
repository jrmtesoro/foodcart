<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    protected $fillable = [
        "token"
    ];
    public $timestamps = true;
    const UPDATED_AT = null;

    protected $table = "password_reset";

    public function user()
    {
        return $this->belongsToMany('App\User', 'user_password_reset');
    }
}
