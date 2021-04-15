<?php

namespace App\Http\Controllers\WebController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;

class LogController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.pages.logs.logs_index');
    }

    public function datatable(Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("GET", $this->url."datatable/allLogs", [
            "headers" => $this->header,
            "query" => $request->all()
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        return $result;
    }

    public function restaurant_datatable($restaurant_id, Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $request = $client->request("GET", $this->url."datatable/restaurant/$restaurant_id/logs", [
            "headers" => $this->header,
            "query" => $request->all()
        ]);

        $result = json_decode($request->getBody()->getContents(), true);

        return $result;
    }

    public function customer_datatable($customer_id, Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $request = $client->request("GET", $this->url."datatable/customer/$customer_id/logs", [
            "headers" => $this->header,
            "query" => $request->all()
        ]);

        $result = json_decode($request->getBody()->getContents(), true);

        return $result;
    }
}
