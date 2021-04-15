<?php

namespace App\Http\Controllers\WebController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use RealRashid\SweetAlert\Facades\Alert;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.pages.users.user_index');
    }

    public function create() {
        return view('admin.pages.users.user_create');
    }

    public function store(Request $request) {
        $client = new Client();
        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("POST", $this->url."admin/users", [
            "headers" => $this->header,
            "form_params" => [
                "reg_fname" => $request->get('reg_fname'),
                "reg_lname" => $request->get('reg_lname'),
                "reg_contact_number" => $request->get('reg_contact_number'),
                "reg_address" => $request->get('reg_address'),
                "reg_email" => $request->get('reg_email')
            ],
            "http_errors" => false
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        if (empty($result)) {
            Alert::error('Add Admin Failed', 'Server Error!');
            return redirect()->back('admin.user.index');
        }
        if (!$result['success']) {
            Alert::error('Add Admin Failed', $result['message']);

            if ($result['message'] == 'Invalid Input') {
                return redirect()->back()
                    ->withErrors($result['errors'])
                    ->withInput($request->all());
            }
            
            return redirect()->back()
                ->withInput($request->all());
        }

        $client1 = new Client();
        $log_store = $client1->request("POST", $this->url."logs", [
            "headers" => $this->header,
            "form_params" => [
                "ip_address" => $request->ip(),
                "type" => "Insert",
                "description" => "Added new admin, user ID".$result['user_id'],
                "origin" => "web"
            ]
        ]);

        Alert::Success('Add Admin Success', $result['message']);
        $data = array(
            "email" => $result['email'],
            "password" => $result['password']
        );

        return view('admin.pages.users.user_index')->with("data", $data);
    }

    public function datatable(Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("get", $this->url."datatable/admin/users", [
            "headers" => $this->header,
            "query" => $request->all()
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        return $result;
    }
}
