<?php

namespace App\Http\Controllers\WebController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Carbon;

class OwnerOrderController extends Controller
{
    public function index(Request $request)
    {
        return view('owner.pages.order.order_index');
    }

    public function datatable(Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("GET", $this->url."datatable/order", [
            "headers" => $this->header,
            "query" => $request->all()
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        return $result;
    }

    public function show($suborder, Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("GET", $this->url."owner/order/$suborder", [
            "headers" => $this->header
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        if (!$result['success']) {
            Alert::error('View Order Failed', $result['message']);
            return redirect()->route('owner.order.index');
        }

        return view('owner.pages.order.order_show')
                ->with('order', $result['data']);
    }

    public function accept($suborder, Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("GET", $this->url."owner/order/$suborder/accept", [
            "headers" => $this->header
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        if (!$result['success']) {
            Alert::error('Accept Order Failed', $result['message']);
            return redirect()->route('owner.order.index');
        }

        $client1 = new Client();
        $log_store = $client1->request("POST", $this->url."logs", [
            "headers" => $this->header,
            "form_params" => [
                "ip_address" => $request->ip(),
                "type" => "Update",
                "description" => "Order #".$result['data']['order_code']." accepted",
                "origin" => "web"
            ]
        ]);

        Alert::success('Accept Order Success', $result['message']);
        return redirect()->back();
    }

    public function reject($suborder, Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("GET", $this->url."owner/order/$suborder/reject", [
            "headers" => $this->header
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        if (!$result['success']) {
            Alert::error('Reject Order Failed', $result['message']);
            return redirect()->route('owner.order.index');
        }

        $client1 = new Client();
        $log_store = $client1->request("POST", $this->url."logs", [
            "headers" => $this->header,
            "form_params" => [
                "ip_address" => $request->ip(),
                "type" => "Update",
                "description" => "Order #".$result['data']['order_code']." rejected",
                "origin" => "web"
            ]
        ]);

        Alert::success('Reject Order Success', $result['message']);
        return redirect()->route('owner.order.index');
    }

    public function deliver($suborder, Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("GET", $this->url."owner/order/$suborder/deliver", [
            "headers" => $this->header
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        if (!$result['success']) {
            Alert::error('Deliver Order Failed', $result['message']);
            return redirect()->route('owner.order.index');
        }

        $client1 = new Client();
        $log_store = $client1->request("POST", $this->url."logs", [
            "headers" => $this->header,
            "form_params" => [
                "ip_address" => $request->ip(),
                "type" => "Update",
                "description" => "Order #".$result['data']['order_code']." delivered",
                "origin" => "web"
            ]
        ]);

        Alert::success('Deliver Order Success', $result['message']);
        return redirect()->back();
    }

    public function complete($suborder, Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("GET", $this->url."owner/order/$suborder/complete", [
            "headers" => $this->header
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        if (!$result['success']) {
            Alert::error('Complete Order Failed', $result['message']);
            return redirect()->route('owner.order.index');
        }

        $client1 = new Client();
        $log_store = $client1->request("POST", $this->url."logs", [
            "headers" => $this->header,
            "form_params" => [
                "ip_address" => $request->ip(),
                "type" => "Update",
                "description" => "Order #".$result['data']['order_code']." completed",
                "origin" => "web"
            ]
        ]);

        Alert::success('Complete Order Success', $result['message']);
        return redirect()->route('owner.order.index');
    }

    public function cancel($suborder, Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("GET", $this->url."owner/order/$suborder/cancel", [
            "headers" => $this->header
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        if (!$result['success']) {
            Alert::error('Cancel Order Failed', $result['message']);
            return redirect()->route('owner.order.index');
        }

        $client1 = new Client();
        $log_store = $client1->request("POST", $this->url."logs", [
            "headers" => $this->header,
            "form_params" => [
                "ip_address" => $request->ip(),
                "type" => "Update",
                "description" => "Order #".$result['data']['order_code']." cancelled",
                "origin" => "web"
            ]
        ]);

        Alert::success('Cancel Order Success', $result['message']);
        return redirect()->route('owner.order.index');
    }
}
