<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserPasswordReset extends Model
{
    protected $fillable = [
        "user_id", "password_reset_id"
    ];

    protected $table = "password_reset";
}
