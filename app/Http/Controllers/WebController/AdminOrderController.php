<?php

namespace App\Http\Controllers\WebController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use RealRashid\SweetAlert\Facades\Alert;

class AdminOrderController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.pages.orders.orders_index');
    }

    public function show($order_id, Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("GET", $this->url."admin/order/$order_id", [
            "headers" => $this->header
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        if (!$result['success']) {
            Alert::error('View Order Failed', $result['message']);
            return redirect()->route('admin.order.index.web');
        }

        return view('admin.pages.orders.orders_show')
                ->with('order', $result['data']);
    }

    public function datatable(Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("get", $this->url."datatable/admin/orders", [
            "headers" => $this->header,
            "query" => $request->all()
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        return $result;
    }
}
