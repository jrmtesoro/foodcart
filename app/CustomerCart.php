<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerCart extends Model
{
    protected $hidden = ['pivot'];
    protected $table = "customer_cart";

    protected $fillable = [
        'customer_id', 'cart_id'
    ];

    public $timestamps = false;
}
