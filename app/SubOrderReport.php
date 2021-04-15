<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubOrderReport extends Model
{
    protected $hidden = ['pivot'];

    public $timestamps = false;

    protected $fillable = [
        "sub_order_id", 'report_id'
    ];
    protected $table = "sub_order_report";
}
