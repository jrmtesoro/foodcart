<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerReport extends Model
{
    protected $hidden = ['pivot'];

    public $timestamps = false;

    protected $fillable = [
        "customer_id", 'report_id'
    ];
    
    protected $table = "customer_report";
}
