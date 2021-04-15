<?php

namespace App\Http\Controllers\WebController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use RealRashid\SweetAlert\Facades\Alert;

class BanController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.pages.ban.ban_index');
    }

    public function datatable(Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("GET", $this->url."datatable/ban", [
            "headers" => $this->header,
            "query" => $request->all()
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        return $result;
    }

    public function show($ban_id, Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("GET", $this->url."admin/ban/$ban_id", [
            "headers" => $this->header
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        if (!$result['success']) {
            Alert::error('View Ban Information Failed', $result['message']);
            return redirect()->route('ban.index');
        }

        return view('admin.pages.ban.ban_show')
                ->with('ban', $result['data']['ban'])
                ->with('user', $result['data']['user'])
                ->with('customer', $result['data']['customer'])
                ->with('reports', $result['data']['reports']);
    }

    public function store(Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("POST", $this->url."admin/ban", [
            "headers" => $this->header,
            "form_params" => $request->all()
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        if (!$result['success']) {
            Alert::error('Ban User Failed', $result['message']);
            return redirect()->back();
        }

        $client1 = new Client();
        $log_store = $client1->request("POST", $this->url."logs", [
            "headers" => $this->header,
            "form_params" => [
                "ip_address" => $request->ip(),
                "type" => "Insert",
                "description" => "Ban User ID : ".$request->get('user_id'),
                "origin" => "web"
            ]
        ]);

        Alert::success('Ban User Success', $result['message']);
        return redirect()->back();
    }

    public function destroy($user_id, Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("GET", $this->url."admin/ban/$user_id/lift", [
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
                "type" => "Delete",
                "description" => "Unban User ID : ".$user_id,
                "origin" => "web"
            ]
        ]);

        return response()->json($result);
    }
}
