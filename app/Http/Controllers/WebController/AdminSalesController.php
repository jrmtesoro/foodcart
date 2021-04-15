<?php

namespace App\Http\Controllers\WebController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use RealRashid\SweetAlert\Facades\Alert;
use Storage;

class AdminSalesController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.pages.sales.sales_index');
    }

    public function datatable(Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("get", $this->url."datatable/admin/sales", [
            "headers" => $this->header,
            "query" => $request->all()
        ]);

        $result = json_decode($req->getBody()->getContents(), true); 

        $params = ['radio_filter', 'column', 'search', 'start_range', 'end_range', 'specific_date'];
        $query = [];
        foreach ($request->all() as $key => $value) {
            if (in_array($key, $params)) {
                $query[$key] = $value;
            }
        }

        $result['export_link'] = route('admin.sales.pdf', $query);

        return $result;
    }

    public function datatable1(Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("get", $this->url."datatable/admin/menusales", [
            "headers" => $this->header,
            "query" => $request->all()
        ]);

        $result = json_decode($req->getBody()->getContents(), true); 

        $params = ['radio_filter', 'column', 'search', 'start_range', 'end_range', 'specific_date'];
        $query = [];
        foreach ($request->all() as $key => $value) {
            if (in_array($key, $params)) {
                $query[$key] = $value;
            }
        }
        $result['export_link'] = route('admin.sales1.pdf', $query);
        
        return $result;
    }

    public function pdf(Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("get", $this->url."admin/sales/restaurant/pdf", [
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
                "description" => "Restaurant Sales Report",
                "origin" => "web"
            ]
        ]);
        
        return Storage::disk('local')->download($result['data']['full_path']);
    }

    public function pdf1(Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("get", $this->url."admin/sales/menu/pdf", [
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
                "description" => "Menu Sales Report",
                "origin" => "web"
            ]
        ]);
        
        return Storage::disk('local')->download($result['data']['full_path']);
    }
}
