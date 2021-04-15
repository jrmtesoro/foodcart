<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MenuCategory extends Model
{
    protected $fillable = [
        'menu_id', 'category_id'
    ];

    protected $hidden = ['pivot'];
    
    protected $table = "menu_category";
    public $timestamps = false;
}
