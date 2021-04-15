<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        "fname", "lname", "contact_number", "address"
    ];

    protected $hidden = ['pivot'];
    
    public $timestamps = true;

    protected $table = "customer";

    public function user()
    {
        return $this->belongsToMany('App\User', 'user_customer');
    }

    public function order()
    {
        return $this->belongsToMany('App\Order', 'customer_order');
    }

    public function cart()
    {
        return $this->belongsToMany('App\Cart', 'customer_cart');
    }

    public function rating()
    {
        return $this->belongsToMany('App\Rating', 'customer_rating');
    }

    public function restaurant()
    {
        return $this->belongsToMany('App\Restaurant', 'favorite');
    }

    public function report()
    {
        return $this->belongsToMany('App\Report', 'customer_report');
    }

    public function register($details)
    {
        $customer = Customer::create($details);
        return $customer;
    }
}
