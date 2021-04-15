<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ban extends Model
{
    protected $fillable = [
        "reason"
    ];

    protected $hidden = ['pivot'];
    
    public $timestamps = true;

    protected $table = "ban";

    public function user()
    {
        return $this->belongsToMany('App\User', 'user_ban');
    }
}
