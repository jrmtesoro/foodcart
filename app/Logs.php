<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Logs extends Model
{
    protected $table = "logs";

    public $timestamp = true;

    const UPDATED_AT = null;

    protected $fillable = [
        "user_id", "type", "description", "origin", "ip_address"
    ];
}
