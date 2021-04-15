<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserChangeRequest extends Model
{
    protected $fillable = [
        "user_id", "change_request_id"
    ];

    protected $hidden = ['pivot'];
    
    public $timestamps = false;
}