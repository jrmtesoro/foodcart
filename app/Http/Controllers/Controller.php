<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\User;
use App\Customer;
use App\Restaurant;
use App\Menu;
use App\Category;
use App\Tag;
use App\PasswordReset;
use App\Permit;
use App\Cart;
use App\ItemList;
use App\Order;
use App\SubOrder;
use App\Rating;
use App\Favorite;
use App\Report;
use App\Admin;
use App\Ban;
use App\Verification;
use App\Logs;
use App\ChangeRequest;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        ini_set('memory_limit','256M');
    }

    protected $url_web = "localhost/fcart/public/";
    protected $url = "localhost/fcart/public/api/";
    protected $header = array(
            "Accept" => "application/json",
            "Origin" => "web"
        );

    public function user() 
    {
        $user = new User();
        return $user;
    }

    public function customer() 
    {
        $customer = new Customer();
        return $customer;
    }

    public function restaurant() 
    {
        $restaurant = new Restaurant();
        return $restaurant;
    }

    public function menu() 
    {
        $menu = new Menu();
        return $menu;
    }

    public function category() 
    {
        $category = new Category();
        return $category;
    }

    public function tag() 
    {
        $tag = new Tag();
        return $tag;
    }

    public function password_reset() 
    {
        $password_reset = new PasswordReset();
        return $password_reset;
    }

    public function permit()
    {
        $permit = new Permit();
        return $permit;
    }

    public function cart()
    {
        $cart = new Cart();
        return $cart;
    }

    public function order()
    {
        $order = new Order();
        return $order;
    }

    public function itemlist()
    {
        $item_list = new ItemList();
        return $item_list;
    }

    public function suborder()
    {
        $sub_order = new SubOrder();
        return $sub_order;
    }

    public function rating()
    {
        $rating = new Rating();
        return $rating;
    }

    public function favorite()
    {
        $favorite = new Favorite();
        return $favorite;
    }

    public function report()
    {
        $report = new Report();
        return $report;
    }

    public function admin()
    {
        $admin = new Admin();
        return $admin;
    }

    public function ban()
    {
        $ban = new Ban();
        return $ban;
    }

    public function verification()
    {
        $verification = new Verification();
        return $verification;
    }

    public function logs()
    {
        $logs = new Logs();
        return $logs;
    }

    public function request_change()
    {
        $change_request = new ChangeRequest();
        return $change_request;
    }
}
