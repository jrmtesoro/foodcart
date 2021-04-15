<?php

namespace App\Http\Controllers\WebController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;

class NotificationController extends Controller
{
    public function orders()
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $request = $client->request("GET", $this->url."owner/notification/orders", [
            "headers" => $this->header
        ]);

        $result = json_decode($request->getBody()->getContents(), true);

        return response()->json($result);
    }

    public function cart()
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $request = $client->request("GET", $this->url."guest/notification/cart", [
            "headers" => $this->header
        ]);

        $result = json_decode($request->getBody()->getContents(), true);

        return response()->json($result);
    }

    public function reports()
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $request = $client->request("GET", $this->url."admin/notification/reports", [
            "headers" => $this->header
        ]);

        $result = json_decode($request->getBody()->getContents(), true);

        return response()->json($result);
    }

    public function tags()
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $request = $client->request("GET", $this->url."admin/notification/tags", [
            "headers" => $this->header
        ]);

        $result = json_decode($request->getBody()->getContents(), true);

        return response()->json($result);
    }

    public function partnership()
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $request = $client->request("GET", $this->url."admin/notification/partnership", [
            "headers" => $this->header
        ]);

        $result = json_decode($request->getBody()->getContents(), true);

        return response()->json($result);
    }
    

    public function admin()
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $request = $client->request("GET", $this->url."admin/notification/admin", [
            "headers" => $this->header
        ]);

        $result = json_decode($request->getBody()->getContents(), true);

        return response()->json($result);
    }
}
