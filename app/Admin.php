<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $fillable = [
        "fname", "lname", "address", "contact_number"
    ];

    protected $hidden = ['pivot'];
    
    public $timestamps = true;

    protected $table = "admin";

    public function user()
    {
        return $this->belongsToMany('App\User', 'user_admin');
    }
}
