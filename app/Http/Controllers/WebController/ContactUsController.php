<?php

namespace App\Http\Controllers\WebController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use RealRashid\SweetAlert\Facades\Alert;

class ContactUsController extends Controller
{
    public function index()
    {
        return view('guest.pages.contactus');
    }

    public function store(Request $request)
    {
        //dont forget the logs
        $client = new Client();

        $req = $client->request("POST", $this->url."contactus", [
            "headers" => $this->header,
            "form_params" => $request->all()
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        if (!$result['success']) {
            Alert::error('Send Message Failed', $result['message']);
            return redirect()->back()->withInput($request->all())
                    ->withErrors($result['errors']);
        }

        $client1 = new Client();
        $log_store = $client1->request("POST", $this->url."logs1", [
            "headers" => $this->header,
            "form_params" => [
                "ip_address" => $request->ip(),
                "type" => "Insert",
                "description" => "Contact Us",
                "origin" => "web"
            ]
        ]);

        Alert::success('Send Message Success', $result['message']);
        return redirect()->route('home');
    }
}
