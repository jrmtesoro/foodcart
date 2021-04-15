<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserCustomer extends Model
{
    protected $fillable = [
        "user_id", "customer_id"
    ];

    protected $hidden = ['pivot'];
    
    public $timestamps = false;
}
