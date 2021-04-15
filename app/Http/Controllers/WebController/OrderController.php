<?php

namespace App\Http\Controllers\WebController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use RealRashid\SweetAlert\Facades\Alert;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("get", $this->url."order/history", [
            "headers" => $this->header
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        if (!$result['success']) {
            return view('guest.pages.order.history');
        }

        return view('guest.pages.order.history')
                ->with('order_history', $result['data']);
    }

    public function show($code, Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("get", $this->url."order/$code", [
            "headers" => $this->header
        ]);
        
        $result = json_decode($req->getBody()->getContents(), true);

        if (!$result['success']) {
            return redirect()->route('order.index');
        }

        $indx = 0;
        foreach ($result['data']['suborder'] as $sub_order) {
            $restaurant = $sub_order['restaurant'];
            $contact = $restaurant['contact_number'];
            $contact_number = "";
            if (strlen($contact) == 7) {
                $contact_number = substr($contact, 0, 3)."-".substr($contact, 2, 2)."-".substr($contact, 4, 2);
            } else {
                $contact_number = "+63-".substr($contact, 0, 4)."-".substr($contact, 3, 4)."-".substr($contact, 7, 3);
            }
            $result['data']['suborder'][$indx]['restaurant']['contact_number'] = $contact_number;

            $indx++;
        }

        return view('guest.pages.order.show')
                ->with('order_details', $result['data']);
    }

    public function store(Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("POST", $this->url."checkout", [
            "headers" => $this->header,
            "form_params" => $request->all()
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        if (!$result['success']) {
            Alert::error('Checkout Failed', $result['message']);
            return redirect()->back()
                    ->withErrors($result['errors'])
                    ->withInput($request->all());
        }

        $client1 = new Client();
        $log_store = $client1->request("POST", $this->url."logs", [
            "headers" => $this->header,
            "form_params" => [
                "ip_address" => $request->ip(),
                "type" => "Insert",
                "description" => "Placed order #".$result['data']['order_code'],
                "origin" => "web"
            ]
        ]);

        Alert::success('Checkout Success', $result['message']);
        return redirect()->route('home');
    }
}
