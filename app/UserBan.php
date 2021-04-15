<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserBan extends Model
{
    protected $fillable = [
        "user_id", "ban_id"
    ];

    protected $hidden = ['pivot'];
    
    public $timestamps = false;

    protected $table = "user_ban";
}
