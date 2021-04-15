<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Report extends Model
{
    protected $table = "report";
    protected $hidden = ['pivot'];

    protected $fillable = [
        'reason', 'code', 'status', 'proof1', 'proof2', 'proof3', 'comment'
    ];

    public $timestamps = true;

    public function customer()
    {
        return $this->belongsToMany('App\Customer', 'customer_report');
    }

    public function restaurant()
    {
        return $this->belongsToMany('App\Restaurant', 'restaurant_report');
    }

    public function suborder()
    {
        return $this->belongsToMany('App\SubOrder', 'sub_order_report');
    }

    public function checkReport($report_code, $restaurant_id)
    {
        return DB::table('report')->select(['*'])
                ->join('restaurant_report', 'restaurant_report.report_id', '=', 'report.id')
                ->join('restaurant', 'restaurant.id', '=', 'restaurant_report.restaurant_id')
                ->where('restaurant.id', $restaurant_id)
                ->where('report.code', $report_code)
                ->get()->first();
    }

    public function getReport($restaurant_id) 
    {
        return DB::table('report')->select(['report.*'])
                ->join('restaurant_report', 'restaurant_report.report_id', '=', 'report.id')
                ->join('restaurant', 'restaurant.id', '=', 'restaurant_report.restaurant_id')
                ->where('restaurant.id', $restaurant_id)
                ->orderBy('report.created_at', 'DESC')
                ->get();
    }
}
