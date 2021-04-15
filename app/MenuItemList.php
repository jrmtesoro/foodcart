<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MenuItemList extends Model
{
    protected $table = "menu_item_list";
    protected $hidden = ['pivot'];
    protected $fillable = [
        'menu_id', 'item_list_id'
    ];

    public $timestamps = false;
}
