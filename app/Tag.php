<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = [
        'name', 'slug', 'status'
    ];

    protected $softDelete = true;

    protected $table = "tag";
    protected $hidden = ['pivot'];

    public $timestamps = true;

    public function menu()
    {
        return $this->belongsToMany('App\Menu', 'menu_tag');
    }

    public function getTags($get, $status)
    {
        $tags = Tag::select($get)->where('status', $status)->orderBy('name', 'asc')->get();
        return $tags;
    }
}
