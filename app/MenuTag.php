<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MenuTag extends Model
{
    protected $fillable = [
        'menu_id', 'tag_id'
    ];

    protected $table = "menu_tag";
    public $timestamps = false;
}
