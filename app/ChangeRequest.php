<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChangeRequest extends Model
{
    protected $table = "change_request";
    protected $hidden = ['pivot'];

    protected $fillable = [
        'reason', 'old_email', 'new_email', 'status'
    ];

    public $timestamps = true;

    public function user()
    {
        return $this->belongsToMany('App\User', 'user_change_request');
    }
}
