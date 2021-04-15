<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Verification extends Model
{
    protected $hidden = ['pivot'];

    public $timestamps = true;

    protected $fillable = [
        "token"
    ];

    const UPDATED_AT = null;

    protected $table = "verification";

    public function user()
    {
        return $this->belongsToMany('App\User', 'user_verification');
    }
}
