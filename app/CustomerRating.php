<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerRating extends Model
{
    protected $hidden = ['pivot'];

    public $timestamps = false;

    protected $fillable = [
        "customer_id", 'rating_id'
    ];
    protected $table = "customer_rating";
}
