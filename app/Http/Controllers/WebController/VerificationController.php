<?php

namespace App\Http\Controllers\WebController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use RealRashid\SweetAlert\Facades\Alert;

class VerificationController extends Controller
{
    public function manual()
    {
        return view('guest.pages.verify');
    }

    public function verify(Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("get", $this->url."email/verify", [
            "headers" => $this->header,
            "query" => [
                "verification_token" => $request->get('verification_token')
            ]
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        if (!$result['success']) {
            Alert::error('Email Verification Failed', $result['message']);
            return redirect()->route('login');
        }

        session()->flush();

        Alert::success('Email Verification Success', $result['message']);
        return redirect()->route('login');
    }

    public function resend()
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("get", $this->url."email/resend", [
            "headers" => $this->header
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        Alert::success('Email Verification Sent', $result['message']);
        return redirect()->route('login')->with('code', $result['data']['code']);
    }
}
