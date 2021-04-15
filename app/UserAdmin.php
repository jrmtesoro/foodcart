<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserAdmin extends Model
{
    protected $hidden = ['pivot'];
    protected $table = "user_admin";

    protected $fillable = [
        'user_id', 'admin_id'
    ];

    public $timestamps = false;
}
