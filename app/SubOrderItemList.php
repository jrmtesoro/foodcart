<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubOrderItemList extends Model
{
    protected $hidden = ['pivot'];

    public $timestamps = false;

    protected $fillable = [
        "sub_order_id", 'item_list_id'
    ];
    protected $table = "sub_order_item_list";
}
