<?php

namespace App\Http\Controllers\WebController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;

class DashboardController extends Controller
{
    public function admin_chart($filter)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("get", $this->url."admin/charts/$filter", [
            "headers" => $this->header
        ]);

        $result = json_decode($req->getBody()->getContents(), true); 

        return response()->json($result);
    }

    public function owner_chart($filter)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("get", $this->url."owner/charts/$filter", [
            "headers" => $this->header
        ]);

        $result = json_decode($req->getBody()->getContents(), true); 

        return response()->json($result);
    }
}
