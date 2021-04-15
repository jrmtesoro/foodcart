<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MenuCart extends Model
{
    protected $table = "menu_cart";
    protected $hidden = ['pivot'];

    protected $fillable = [
        'menu_id', 'cart_id'
    ];

    public $timestamps = false;
}
