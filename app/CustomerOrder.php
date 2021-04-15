<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerOrder extends Model
{
    protected $hidden = ['pivot'];
    protected $table = "customer_order";

    protected $fillable = [
        'customer_id', 'order_id'
    ];

    public $timestamps = false;
}
