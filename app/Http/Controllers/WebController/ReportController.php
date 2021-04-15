<?php

namespace App\Http\Controllers\WebController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use RealRashid\SweetAlert\Facades\Alert;

class ReportController extends Controller
{
    public function owner(Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("get", $this->url."owner/report", [
            "headers" => $this->header
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        return view('owner.pages.report.report_index')
                ->with('reports', $result['data']);
    }

    public function owner_show($report_code, Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("get", $this->url."owner/report/$report_code", [
            "headers" => $this->header
        ]);
        
        $result = json_decode($req->getBody()->getContents(), true);

        if (!$result['success']) {
            Alert::error('View Report Failed!', $result['message']);
            return redirect()->route('report.owner.index');
        }

        return view('owner.pages.report.report_show')
                ->with('report', $result['data']);
    }

    public function admin1(Request $request)
    {
        return view('admin.pages.report.report_index');
    }

    public function admin_show($report_code, Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("get", $this->url."admin/report/$report_code", [
            "headers" => $this->header
        ]);
        
        $result = json_decode($req->getBody()->getContents(), true);

        if (!$result['success']) {
            Alert::error('View Report Failed!', $result['message']);
            return redirect()->route('admin.report.index');
        }

        return view('admin.pages.report.report_show')
                ->with('report', $result['data']);
    }

    public function datatable(Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("get", $this->url."datatable/report", [
            "headers" => $this->header,
            "query" => $request->all()
        ]);
        
        $result = json_decode($req->getBody()->getContents(), true);

        return $result;
    }

    public function store(Request $request)
    {
        $image_array = ['report_proof1', 'report_proof2', 'report_proof3'];
        foreach ($request->all() as $key => $value) 
        {
            $temp_array = [];
            $temp_array['name'] = $key;
            if (in_array($key, $image_array) && $value !== null) 
            {
                $temp_array['contents'] = fopen($value->getPathname(), 'r');
                $temp_array['Mime-Type'] = $value->getmimeType();
                $temp_array['filename'] = $value->getClientOriginalName();
            }
            else
            {
                $temp_array['contents'] = $value;
            }

            $multipart[] = $temp_array;
        }

        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("POST", $this->url."owner/report", [
            "headers" => $this->header,
            "multipart" => $multipart,
            "http_errors" => false
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        if (!$result['success']) {
            if (empty($result['errors'])) {
                Alert::error('Send Report Failed!', $result['message']);
                return redirect()->back()
                    ->withErrors([])
                    ->withInput($request->all());
            }
            $html = "";
            foreach ($result['errors'] as $error) {

                $html .= "<br>".$error[0];
            }
            Alert::html($html, 'error', $result['message']);
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
                "description" => "Ticket #".$result['data']['report_code'],
                "origin" => "web"
            ]
        ]);

        Alert::success('Order Report Success', $result['message']);
        return redirect()->route('owner.order.index');
    }

    public function investigate($report_code, Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("get", $this->url."admin/report/$report_code/investigate", [
            "headers" => $this->header
        ]);
        
        $result = json_decode($req->getBody()->getContents(), true);

        if (!$result['success']) {
            Alert::error('Update Report Failed!', $result['message']);
            return redirect()->route('admin.report.index');
        }

        $client1 = new Client();
        $log_store = $client1->request("POST", $this->url."logs", [
            "headers" => $this->header,
            "form_params" => [
                "ip_address" => $request->ip(),
                "type" => "Update",
                "description" => "Ticket #".$report_code." investigating",
                "origin" => "web"
            ]
        ]);

        Alert::success('Update Report Success', $result['message']);
        return redirect()->route('admin.report.show', ['report_code' => $report_code]);
    }

    public function close($report_code, Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("post", $this->url."admin/report/$report_code/close", [
            "headers" => $this->header,
            "form_params" => $request->all()
        ]);
        
        $result = json_decode($req->getBody()->getContents(), true);

        if (!$result['success']) {
            if (empty($result['errors'])) {
                Alert::error('Update Report Failed!', $result['message']);
                return redirect()->route('admin.report.index');
            }
            
            $html = "";
            foreach ($result['errors'] as $error) {

                $html .= "<br>".$error[0];
            }
            Alert::html($html, 'error', $result['message']);
            return redirect()->back()
                    ->withErrors($result['errors'])
                    ->withInput($request->all());
        }

        if ($result['data']['desc'] != "") {
            $client2 = new Client();
            $log_store1 = $client2->request("POST", $this->url."logs", [
                "headers" => $this->header,
                "form_params" => [
                    "ip_address" => $request->ip(),
                    "type" => "Update",
                    "description" => $result['data']['desc'],
                    "origin" => "web"
                ]
            ]);
        }

        $client1 = new Client();
        $log_store = $client1->request("POST", $this->url."logs", [
            "headers" => $this->header,
            "form_params" => [
                "ip_address" => $request->ip(),
                "type" => "Update",
                "description" => "Ticket #".$report_code." closed",
                "origin" => "web"
            ]
        ]);

        Alert::success('Close Report Success', $result['message']);
        return redirect()->route('admin.report.index');
    }
}
