<?php

namespace App\Http\Controllers\WebController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use RealRashid\SweetAlert\Facades\Alert;

class CartController extends Controller
{
    public function index()
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("get", $this->url."guest/cart", [
            "headers" => $this->header
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        if (!$result['success']) {
            return response()->json([
                "success" => false,
                "html" => 
                "<div class='row border-bottom border-top'>
                    <div class='col-12 py-4'>
                        <p class='text-center'>No Item Found!</p>
                    </div>
                </div>"
            ]);
        }

        $html = "";
        $grand_total = 0;
        $total_cooking_time = 0;
        $total_flat_rate = 0;

        foreach ($result['data'] as $restaurant) {
            $restaurant_link = route('guest.restaurant', ['slug' => $restaurant['slug']]);
            $temp1 = 
            "<div class='row border-bottom'>
                <div class='col-12'>
                    <div class='d-flex'>
                        <a class='h6 text-info' href='$restaurant_link'>".$restaurant['name']."</a>
                    </div>
                </div>
                <div class='col-12'>
                    <div class='d-flex'>
                        <p class='h6 mr-auto'>Delivery Time : ".$restaurant['eta']." mins</p>
                        <p class='h6'>Flat Rate : ₱ ".$restaurant['flat_rate'].".00</p>
                    </div>
                </div>
            </div>";
        
            $temp = "";
            $temp_time = 0;
            $total_flat_rate += $restaurant['flat_rate'];
            $total = $restaurant['flat_rate'];
            foreach ($restaurant['menu'] as $menu) {
                $image = !empty($menu['image_name']) ? route('photo.menu', ['slug' => $menu['image_name']]).'?size=thumbnail' : asset('img/alt.png');
                $temp2 = 
                "<div class='col-lg-6'>
                    <div class='row'>
                        <div class='col-auto'>
                            <img src='".$image."' width='100' height='100'>
                        </div>
                        <div class='col'>
                            <div class='d-flex justify-content-between'>
                                <p class='h6'>".$menu['name']."</p>
                                <p class='h6'>QTY: x".$menu['quantity']."</p>
                            </div>
                            <div class='d-flex justify-content-between'>
                                <p class='h6'>".$menu['cooking_time']." mins.</p>
                            </div>
                            <div class='d-flex'>
                                <div class='mr-auto'>
                                    <button type='button' class='btn btn-warning btn-sm' data-toggle='modal' data-id='".$menu['id']."' data-quantity='".$menu['quantity']."' data-target='#editQuantity'>Edit</button>
                                    <button type='button' class='btn btn-danger btn-sm delete-btn' onclick='deleteBtn(this)' data-id='".$menu['id']."'>Delete</button>
                                </div>
                                <p class='h6'>₱ ".$menu['price'].".00</p>
                            </div>
                        </div>
                    </div>
                </div>";
                
                $multiplier = ceil($menu['quantity']/5);
                $temp_time += $menu['cooking_time'] * $multiplier;
                $total += $menu['price'] * $menu['quantity'];

                $temp .= $temp2;
            }

            $temp_time += $restaurant['eta'];
            if ($total_cooking_time < $temp_time) {
                $total_cooking_time = $temp_time;
            }

            $grand_total += $total;
            $html .= $temp1."<div class='row pt-4'>".$temp."</div><hr>
            <div class='d-flex'>
                <p class='h6 mr-auto'>Estimated Time : ".$temp_time."mins</p>
                <p class='h6'>Total: ₱".$total.".00</p>
            </div><hr>";
        }
        $html .= 
        "<div class='row'>
            <div class='offset-lg-8 col-lg-4'>
                <div class='d-flex justify-content-between'>
                    <p class='h6'>Estimated Time</p><p class='h6'>".$total_cooking_time." mins</p>
                </div>
                <div class='d-flex justify-content-between'>
                    <p class='h6'>Total Flat Rate</p><p class='h6'>₱ ".$total_flat_rate.".00</p>
                </div>
                <div class='d-flex justify-content-between'>
                    <p class='h6'>Grand Total</p><p class='h6'>₱ ".$grand_total.".00</p>
                </div>
            </div>
        </div>";

        return response()->json([
            "success" => true,
            "html" => $html
        ]);
    }
    
    public function store(Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("POST", $this->url."guest/cart", [
            "headers" => $this->header,
            "form_params" => [
                "menu_slug" => $request->get('menu_slug'),
                "quantity" => $request->get('quantity')
            ]
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        $client1 = new Client();
        $log_store = $client1->request("POST", $this->url."logs", [
            "headers" => $this->header,
            "form_params" => [
                "ip_address" => $request->ip(),
                "type" => "Insert",
                "description" => "Added Menu Slug : ".$request->get('menu_slug')." to cart",
                "origin" => "web"
            ]
        ]);

        return response()->json($result);
    }

    public function destroy($cart_id)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("delete", $this->url."guest/cart/$cart_id", [
            "headers" => $this->header
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        return response()->json($result);
    }

    public function empty()
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("get", $this->url."guest/cart/empty", [
            "headers" => $this->header
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        return response()->json($result);
    }

    public function update($cart, Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("POST", $this->url."guest/cart/$cart", [
            "headers" => $this->header,
            "form_params" => [
                "quantity" => $request->get('quantity'),
                "cart_id" => $cart
            ]
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        if (!$result['success']) {
            $temp = $result['message'];
            if (!empty($result['errors'])) {
                foreach ($result['errors'] as $error) {
                    foreach($error as $msg) {
                        $temp .= "<li>$msg</li>";
                    }
                }
            }

            return response()->json([
                "success" => false,
                "html" => $temp
            ]);          
        }

        return response()->json($result);
    }
}
