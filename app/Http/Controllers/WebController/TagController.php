<?php

namespace App\Http\Controllers\WebController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use RealRashid\SweetAlert\Facades\Alert;

class TagController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.pages.tag.tag_index');
    }

    public function datatable(Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("GET", $this->url."datatable/tag", [
            "headers" => $this->header,
            "query" => $request->all()
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        return $result;
    }

    public function store(Request $request)
    {
        $form_params = array(
            "tag_name" => $request->get('tag_name')
        );

        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("POST", $this->url."admin/tag", [
            "headers" => $this->header,
            "form_params" => $form_params
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        $client1 = new Client();
        $log_store = $client1->request("POST", $this->url."logs", [
            "headers" => $this->header,
            "form_params" => [
                "ip_address" => $request->ip(),
                "type" => "Insert",
                "description" => "Tag Name : ".$request->get('tag_name'),
                "origin" => "web"
            ]
        ]);

        return response()->json($result);
    }

    public function reject($tag, Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("GET", $this->url."admin/tag/$tag/reject", [
            "headers" => $this->header
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        $client1 = new Client();
        $log_store = $client1->request("POST", $this->url."logs", [
            "headers" => $this->header,
            "form_params" => [
                "ip_address" => $request->ip(),
                "type" => "Update",
                "description" => "Tag Name : ".$result['data']['tag_name']." rejected",
                "origin" => "web"
            ]
        ]);

        return response()->json($result);
    }

    public function accept($tag, Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("GET", $this->url."admin/tag/$tag/accept", [
            "headers" => $this->header
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        $client1 = new Client();
        $log_store = $client1->request("POST", $this->url."logs", [
            "headers" => $this->header,
            "form_params" => [
                "ip_address" => $request->ip(),
                "type" => "Update",
                "description" => "Tag Name : ".$result['data']['tag_name']." accepted",
                "origin" => "web"
            ]
        ]);

        return response()->json($result);
    }

}
