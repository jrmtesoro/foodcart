<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permit extends Model
{
    protected $fillable = [
        "image_name"
    ];
    protected $hidden = ['pivot'];

    public $timestamps = false;
    protected $table = "permit";

    public function restaurant()
    {
        return $this->belongsToMany('App\Restaurant', 'restaurant_permit');
    }
}
