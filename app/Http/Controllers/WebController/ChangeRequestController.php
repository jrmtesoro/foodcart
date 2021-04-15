<?php

namespace App\Http\Controllers\WebController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use RealRashid\SweetAlert\Facades\Alert;

class ChangeRequestController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.pages.request.request_index');
    }

    public function store(Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("POST", $this->url."request", [
            "headers" => $this->header,
            "form_params" => $request->all()
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        $client1 = new Client();
        $log_store = $client1->request("POST", $this->url."logs", [
            "headers" => $this->header,
            "form_params" => [
                "ip_address" => $request->ip(),
                "type" => "Insert",
                "description" => "Requested change email address",
                "origin" => "web"
            ]
        ]);

        if (!$result['success']) {
            Alert::error('Change Email Request Failed', $result['message']);
            if (!empty($result['errors'])) {
                return redirect()->back()
                    ->with('change_email_error', $result['errors']);
            }
            return redirect()->back();
        }

        Alert::success('Change Email Request Sent', $result['message']);
        return redirect()->back();
    }

    public function accept($request_change_id, Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("get", $this->url."admin/request/$request_change_id/accept", [
            "headers" => $this->header
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        if (!$result['success']) {
            return response()->json($result);
        }

        $client1 = new Client();
        $log_store = $client1->request("POST", $this->url."logs", [
            "headers" => $this->header,
            "form_params" => [
                "ip_address" => $request->ip(),
                "type" => "Update",
                "description" => "Change email request of User ID : ".$result['data']['user_id']." accepted",
                "origin" => "web"
            ]
        ]);

        return response()->json($result);
    }

    public function reject($request_change_id, Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("get", $this->url."admin/request/$request_change_id/reject", [
            "headers" => $this->header
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        if (!$result['success']) {
            return response()->json($result);
        }

        $client1 = new Client();
        $log_store = $client1->request("POST", $this->url."logs", [
            "headers" => $this->header,
            "form_params" => [
                "ip_address" => $request->ip(),
                "type" => "Update",
                "description" => "Change email request of User ID : ".$result['data']['user_id']." rejected",
                "origin" => "web"
            ]
        ]);

        return response()->json($result);
    }

    public function datatable(Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("GET", $this->url."datatable/changerequest", [
            "headers" => $this->header,
            "query" => $request->all()
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        return $result;
    }
}
