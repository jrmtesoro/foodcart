<?php

namespace App\Http\Controllers\WebController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Storage;
use RealRashid\SweetAlert\Facades\Alert;


class OwnerSalesController extends Controller
{
    public function index(Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("GET", $this->url."owner/order/check", [
            "headers" => $this->header
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        if (!$result['success']) {
            Alert::error('View Sales Failed', 'No completed orders found!');
            return redirect()->back();
        }

        return view('owner.pages.sales.sales_index');
    }

    public function datatable(Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("GET", $this->url."datatable/owner/sales", [
            "headers" => $this->header,
            "query" => $request->all()
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        $params = ['radio_filter', 'specific_date', 'start_range', 'end_range'];
        $query = [];
        foreach ($request->all() as $key => $value) {
            if (in_array($key, $params)) {
                $query[$key] = $value;
            }
        }

        $result['export_link'] = route('owner.sales.pdf.menu', $query);   

        return $result;
    }

    public function datatable1(Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("GET", $this->url."datatable/owner/restaurant_sales", [
            "headers" => $this->header,
            "query" => $request->all()
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        $params = ['interval', 'daily_input', 'weekly_input', 'monthly_input', 'yearly_input'];
        $query = [];
        foreach ($request->all() as $key => $value) {
            if (in_array($key, $params)) {
                $query[$key] = $value;
            }
        }

        $result['export_link'] = route('owner.sales.pdf.restaurant', $query);   

        return $result;
    }

    public function pdf(Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("get", $this->url."owner/sales/menu/pdf", [
            "headers" => $this->header,
            "query" => $request->all(),
            "http_errors" => false
        ]);

        $result = json_decode($req->getBody()->getContents(), true); 

        $client1 = new Client();
        $log_store = $client1->request("POST", $this->url."logs", [
            "headers" => $this->header,
            "form_params" => [
                "ip_address" => $request->ip(),
                "type" => "Print",
                "description" => "Menu Sales Report",
                "origin" => "web"
            ]
        ]);
        
        return Storage::disk('local')->download($result['data']['full_path']);
    }

    public function pdf1(Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("get", $this->url."owner/sales/restaurant/pdf", [
            "headers" => $this->header,
            "query" => $request->all()
        ]);

        $result = json_decode($req->getBody()->getContents(), true); 

        $client1 = new Client();
        $log_store = $client1->request("POST", $this->url."logs", [
            "headers" => $this->header,
            "form_params" => [
                "ip_address" => $request->ip(),
                "type" => "Print",
                "description" => "Restaurant Sales Report",
                "origin" => "web"
            ]
        ]);
        
        return Storage::disk('local')->download($result['data']['full_path']);
    }
}
