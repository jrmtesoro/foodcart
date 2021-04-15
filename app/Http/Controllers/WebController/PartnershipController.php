<?php

namespace App\Http\Controllers\WebController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use RealRashid\SweetAlert\Facades\Alert;

class PartnershipController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.pages.partnership.partnership_index');
    }

    public function show($restaurant, Request $r)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $request = $client->request("GET", $this->url."admin/partnership/$restaurant", [
            "headers" => $this->header
        ]);

        $result = json_decode($request->getBody()->getContents(), true);

        if (!$result['success']) {
            Alert::error('View Application Error', $result['message']);
            return redirect()->route('partnership.index');
        }

        return view('admin.pages.partnership.partnership_show')->with('restaurant_info', $result['data']);
    }

    public function accept($restaurant, Request $r)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $request = $client->request("GET", $this->url."admin/partnership/$restaurant/accept", [
            "headers" => $this->header
        ]);

        $result = json_decode($request->getBody()->getContents(), true);

        $client1 = new Client();
        $log_store = $client1->request("POST", $this->url."logs", [
            "headers" => $this->header,
            "form_params" => [
                "ip_address" => $r->ip(),
                "type" => "Update",
                "description" => "Restaurant ID : ".$restaurant." accepted",
                "origin" => "web"
            ]
        ]);

        return $result;
    }

    public function reject($restaurant, Request $r)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $request = $client->request("GET", $this->url."admin/partnership/$restaurant/reject", [
            "headers" => $this->header
        ]);

        $result = json_decode($request->getBody()->getContents(), true);

        $client1 = new Client();
        $log_store = $client1->request("POST", $this->url."logs", [
            "headers" => $this->header,
            "form_params" => [
                "ip_address" => $r->ip(),
                "type" => "Update",
                "description" => "Restaurant ID : ".$restaurant." rejected",
                "origin" => "web"
            ]
        ]);

        return $result;
    }

    public function review($restaurant, Request $r)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $request = $client->request("GET", $this->url."admin/partnership/$restaurant/review", [
            "headers" => $this->header
        ]);

        $result = json_decode($request->getBody()->getContents(), true);

        $client1 = new Client();
        $log_store = $client1->request("POST", $this->url."logs", [
            "headers" => $this->header,
            "form_params" => [
                "ip_address" => $r->ip(),
                "type" => "Update",
                "description" => "Restaurant ID : ".$restaurant." review",
                "origin" => "web"
            ]
        ]);

        return $result;
    }

    public function status(Request $request) {
        $client = new Client();
        $req = $client->request("post", $this->url."partner/status", [
            "headers" => $this->header,
            "form_params" => [
                "partner_email" => $request->get('partner_email')
            ]
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        if (!$result['success']) {
            Alert::error('Check Application Status Error', $result['message']);
            return redirect()->back();
        }

        $data = [
            "email" => $request->get('partner_email'),
            "status" => $result['message']
        ];

        return view('guest.pages.partnership')
                ->withInput($request->all())
                ->with('details', $data);
    }

    public function datatable(Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $request = $client->request("GET", $this->url."datatable/partnership", [
            "headers" => $this->header,
            "query" => $request->all()
        ]);

        $result = json_decode($request->getBody()->getContents(), true);

        return $result;
    }
}
