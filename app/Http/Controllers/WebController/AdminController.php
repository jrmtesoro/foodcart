<?php

namespace App\Http\Controllers\WebController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use RealRashid\SweetAlert\Facades\Alert;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.pages.dashboard');
    }

    public function show(Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("GET", $this->url."admin/profile", [
            "headers" => $this->header
        ]);

        $result = json_decode($req->getBody()->getContents(), true);
        
        return view('admin.pages.profile')
                ->with('admin', $result['data']['admin'])
                ->with('user', $result['data']['user']);
    }

    public function update(Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("POST", $this->url."admin/profile", [
            "headers" => $this->header,
            "form_params" => $request->all()
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        if (!$result['success']) {
            Alert::error('Update Profile Failed', $result['message']);
            return redirect()->back()
                ->withInput($request->all())
                ->with('admin_error', $result['errors']);
        }

        $session_values = $result['data'];

        session($session_values);

        $client1 = new Client();
        $log_store = $client1->request("POST", $this->url."logs", [
            "headers" => $this->header,
            "form_params" => [
                "ip_address" => $request->ip(),
                "type" => "Update",
                "description" => "Admin Profile",
                "origin" => "web"
            ]
        ]);

        Alert::success('Update Profile Success', $result['message']);
        return redirect()->back();
    }
}
