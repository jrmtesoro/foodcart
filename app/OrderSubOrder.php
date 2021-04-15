<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderSubOrder extends Model
{
    protected $hidden = ['pivot'];

    public $timestamps = false;

    protected $fillable = [
        "order_id", 'sub_order_id'
    ];
    protected $table = "order_sub_order";
}
